<?php

namespace Tonysm\TailwindCss\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    protected $signature = '
        tailwindcss:install
        {--download : If you also want to download the Tailwind CSS binary.}
        {--cli-version= : You may override the configured version for the CLI.}
    ';

    protected $description = 'Installs the Tailwind CSS scaffolding for new Laravel applications.';

    public function handle()
    {
        $this->info('Installing the Tailwind CSS scaffolding...');

        $this->copyStubToAppIfMissing(
            stub: __DIR__ . '/../../stubs/tailwind.config.js',
            to: base_path('tailwind.config.js'),
        );

        $this->copyStubToAppIfMissing(
            stub: __DIR__ . '/../../stubs/resources/css/app.css',
            to: resource_path('css/app.css'),
        );

        if ($this->option('download')) {
            $this->call('tailwindcss:download', [
                '--cli-version' => $this->option('version') ?: config('tailwindcss.version'),
            ]);
        } else {
            $this->info('Done!');
        }

        return self::SUCCESS;
    }

    private function copyStubToAppIfMissing(string $stub, string $to): void
    {
        if (File::exists($to)) {
            $this->warn(sprintf("  File %s already exists.", $this->relativeOf($to)));

            return;
        }

        File::ensureDirectoryExists(dirname($to));
        File::copy($stub, $to);

        $this->info(sprintf("  Created the %s file.", $this->relativeOf($to)));
    }

    private function relativeOf(string $path): string
    {
        return Str::after($path, rtrim(base_path(), '/') . '/');
    }
}
