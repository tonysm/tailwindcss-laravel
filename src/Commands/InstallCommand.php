<?php

namespace Tonysm\TailwindCss\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process as SymfonyProcess;
use Tonysm\TailwindCss\Actions\AppendTailwindTag;

class InstallCommand extends Command
{
    protected $signature = '
        tailwindcss:install
        {--download : If you also want to download the Tailwind CSS binary.}
        {--cli-version= : You may override the configured version for the CLI.}
        {--no-tty : Disables TTY output mode. Use this in environments where TTY is not supported or causing issues.}
    ';

    protected $description = 'Installs the Tailwind CSS scaffolding for new Laravel applications.';

    public function handle(): int
    {
        $this->ensureTailwindConfigExists();
        $this->ensureTailwindCliBinaryExists();
        $this->appendTailwindStylesToLayouts();
        $this->installMiddleware('\Tonysm\TailwindCss\Http\Middleware\AddLinkHeaderForPreloadedAssets::class');
        $this->addIngoreLines();
        $this->runFirstBuild();

        $this->newLine();

        $this->components->info('Tailwind CSS Laravel was installed successfully.');

        return self::SUCCESS;
    }

    protected function phpBinary(): string
    {
        return (new PhpExecutableFinder)->find(false) ?: 'php';
    }

    private function ensureTailwindConfigExists(): void
    {
        $this->copyStubToApp(
            stub: __DIR__.'/../../stubs/postcss.config.js',
            to: base_path('postcss.config.js'),
        );

        if (! File::exists($appCssFilePath = resource_path('css/app.css')) || in_array(trim(File::get($appCssFilePath)), ['', '0'], true) || $this->mainCssIsDefault($appCssFilePath)) {
            $this->copyStubToApp(
                stub: __DIR__.'/../../stubs/resources/css/app.css',
                to: $appCssFilePath,
            );
        }
    }

    private function ensureTailwindCliBinaryExists(): void
    {
        if (! File::exists(config('tailwindcss.bin_path')) || $this->option('download')) {
            Process::forever()->tty(SymfonyProcess::isTtySupported())->run([
                $this->phpBinary(),
                'artisan',
                'tailwindcss:download',
                '--cli-version',
                $this->option('cli-version') ?: config('tailwindcss.version'),
            ], function ($_type, $output): void {
                $this->output->write($output);
            });
        }
    }

    private function copyStubToApp(string $stub, string $to): void
    {
        File::ensureDirectoryExists(dirname($to));
        File::copy($stub, $to);
    }

    /**
     * Install the middleware to a group in the application Http Kernel.
     *
     * @param  string  $group
     */
    private function installMiddlewareAfter(string $after, string $name, $group = 'web'): void
    {
        $httpKernel = file_get_contents(app_path('Http/Kernel.php'));

        $middlewareGroups = Str::before(Str::after($httpKernel, '$middlewareGroups = ['), '];');
        $middlewareGroup = Str::before(Str::after($middlewareGroups, "'{$group}' => ["), '],');

        if (str_contains($middlewareGroup, $name)) {
            return;
        }

        $modifiedMiddlewareGroup = str_replace(
            $after.',',
            $after.','.PHP_EOL.'            '.$name.',',
            $middlewareGroup,
        );

        file_put_contents(app_path('Http/Kernel.php'), str_replace(
            $middlewareGroups,
            str_replace($middlewareGroup, $modifiedMiddlewareGroup, $middlewareGroups),
            $httpKernel
        ));
    }

    private function appendTailwindStylesToLayouts(): void
    {
        $this->existingLayoutFiles()
            ->each(fn ($file) => File::put(
                $file,
                (new AppendTailwindTag)(File::get($file)),
            ));
    }

    private function existingLayoutFiles()
    {
        return collect(['app', 'guest'])
            ->map(fn ($file) => resource_path("views/layouts/{$file}.blade.php"))
            ->filter(fn ($file) => File::exists($file));
    }

    private function installMiddleware(string $middleware): void
    {
        if (file_exists(app_path('Http/Kernel.php'))) {
            $this->installMiddlewareAfter('SubstituteBindings::class', $middleware);
        } else {
            $this->installMiddlewareToBootstrap($middleware);
        }
    }

    private function installMiddlewareToBootstrap(string $middleware, string $group = 'web', string $modifier = 'append'): void
    {
        $bootstrapApp = file_get_contents(base_path('bootstrap/app.php'));

        if (str_contains($bootstrapApp, $middleware)) {
            return;
        }

        $bootstrapApp = str_replace(
            '->withMiddleware(function (Middleware $middleware) {',
            '->withMiddleware(function (Middleware $middleware) {'
                .PHP_EOL."        \$middleware->{$group}({$modifier}: ["
                .PHP_EOL."            {$middleware},"
                .PHP_EOL.'        ]);'
                .PHP_EOL,
            $bootstrapApp,
        );

        file_put_contents(base_path('bootstrap/app.php'), $bootstrapApp);
    }

    private function addIngoreLines(): void
    {
        $binary = basename((string) config('tailwindcss.bin_path'));

        if (str_contains(File::get(base_path('.gitignore')), $binary)) {
            return;
        }

        File::append(base_path('.gitignore'), <<<LINES

        /public/css/
        /public/dist/
        .tailwindcss-manifest.json
        {$binary}
        LINES);
    }

    private function runFirstBuild(): void
    {
        $this->call('tailwindcss:build', [
            '--no-tty' => $this->option('no-tty'),
        ]);
    }

    private function mainCssIsDefault($appCssFilePath): bool
    {
        return trim(File::get($appCssFilePath)) === trim(<<<'CSS'
        @tailwind base;
        @tailwind components;
        @tailwind utilities;
        CSS);
    }
}
