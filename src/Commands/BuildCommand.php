<?php

namespace Tonysm\TailwindCss\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class BuildCommand extends Command
{
    protected $signature = '
        tailwindcss:build
        {--watch : If you want to keep the process running to watch your local file changes.}
        {--minify : If you want the final CSS file to be minified.}
        {--digest : If you want the final CSS file to be generated using a digest of its contents (does not work with the --watch flag).}
    ';
    protected $description = 'Generates a new build of Tailwind CSS for your project.';

    public function handle()
    {
        $binFile = config('tailwindcss.bin_path');

        if (! File::exists($binFile)) {
            $this->error('Could not find the Tailwind CSS binary. Please, run the `tailwindcss:download` before trying to build or configure the path to the binary in your config/tailwindcss.php file.');

            return self::FAILURE;
        }

        $this->info('Building assets...');

        $generatedFile = config('tailwindcss.build.destination_file_path');
        $generatedFileRelativePath = Str::after($generatedFile, rtrim(public_path(), '/'));

        File::ensureDirectoryExists(dirname($generatedFile));
        File::cleanDirectory(dirname($generatedFile));

        if ($this->option('watch') || ! $this->option('digest')) {
            // Ensure there is at least one mix-manifest.json that points to the unversioned asset...
            File::put(public_path('/tailwindcss-manifest.json'), json_encode([
                '/css/app.css' => $generatedFileRelativePath,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        $process = new Process(array_filter([
            $binFile,
            '-i', config('tailwindcss.build.source_file_path'),
            '-o', $generatedFile,
            $this->option('watch') ? '-w' : null,
            $this->option('minify') ? '-m' : null,
        ]), timeout: null);

        $process->setPty(true);

        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if ($this->option('digest')) {
            $generatedFile = $this->ensureAssetIsVersioned($generatedFile);
        }

        if (! $this->option('watch') && $this->option('digest')) {
            $this->info('Generating the versioned tailwindcss-manifest.json file...');

            File::put(public_path('/tailwindcss-manifest.json'), json_encode([
                '/css/app.css' => Str::after($generatedFile, rtrim(public_path(), '/')),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        $this->info('Done!');

        return self::SUCCESS;
    }

    protected function ensureAssetIsVersioned(string $generatedFile): string
    {
        $digest = sha1_file($generatedFile);

        $versionedFile = preg_replace(
            '/(\.css)$/',
            sprintf('-%s$1', $digest),
            $generatedFile,
        );

        File::move($generatedFile, $versionedFile);

        return $versionedFile;
    }
}
