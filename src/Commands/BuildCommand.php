<?php

namespace Tonysm\TailwindCss\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Tonysm\TailwindCss\Manifest;

class BuildCommand extends Command
{
    protected $signature = '
        tailwindcss:build
        {--watch : If you want to keep the process running to watch your local file changes.}
        {--minify : If you want the final CSS file to be minified.}
        {--digest : If you want the final CSS file to be generated using a digest of its contents (does not work with the --watch flag).}
        {--prod : This option combines the --minify and --digest options. Ideal for production.}
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

        $sourcePath = $this->fixFilePathForOs(config('tailwindcss.build.source_file_path'));
        $sourceRelativePath = str_replace(rtrim(resource_path(), DIRECTORY_SEPARATOR), '', $sourcePath);
        $destinationPath = $this->fixFilePathForOs(config('tailwindcss.build.destination_path'));
        $destinationFileAbsolutePath = $destinationPath . DIRECTORY_SEPARATOR . trim($sourceRelativePath, DIRECTORY_SEPARATOR);
        $destinationFileRelativePath = str_replace(rtrim(public_path(), DIRECTORY_SEPARATOR), '', $destinationFileAbsolutePath);

        File::ensureDirectoryExists(dirname($destinationFileAbsolutePath));
        File::cleanDirectory(dirname($destinationFileAbsolutePath));

        if ($this->option('watch') || ! $this->shouldVersion()) {
            // Ensure there is at least one mix-manifest.json that points to the unversioned asset...
            File::put(Manifest::path(), json_encode([
                $this->fixOsFilePathToUriPath($sourceRelativePath) => $this->fixOsFilePathToUriPath($destinationFileRelativePath),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        $process = new Process(array_filter([
            $binFile,
            '-i', $sourcePath,
            '-o', $destinationFileAbsolutePath,
            $this->option('watch') ? '-w' : null,
            $this->shouldMinify() ? '-m' : null,
        ]), timeout: null);
        $process->setWorkingDirectory(base_path());
        $process->setPty(true);

        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if ($this->shouldVersion()) {
            $destinationFileAbsolutePath = $this->ensureAssetIsVersioned($destinationFileAbsolutePath);
            $destinationFileRelativePath = str_replace(rtrim(public_path(), DIRECTORY_SEPARATOR), '', $destinationFileAbsolutePath);
        }

        if (! $this->option('watch') && $this->shouldVersion()) {
            $this->info(sprintf('Generating the versioned %s file...', Manifest::filename()));

            File::put(Manifest::path(), json_encode([
                $this->fixOsFilePathToUriPath($sourceRelativePath) => $this->fixOsFilePathToUriPath($destinationFileRelativePath),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        $this->info('Done!');

        return self::SUCCESS;
    }

    private function fixFilePathForOs(string $path): string
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }

    private function fixOsFilePathToUriPath(string $path): string
    {
        return str_replace(DIRECTORY_SEPARATOR, '/', $path);
    }

    private function shouldVersion(): bool
    {
        return $this->option('digest') || $this->option('prod');
    }

    private function shouldMinify(): bool
    {
        return $this->option('minify') || $this->option('prod');
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
