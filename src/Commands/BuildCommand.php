<?php

namespace Tonysm\TailwindCss\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Process\FakeInvokedProcess;
use Illuminate\Process\InvokedProcess;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process as SymfonyProcess;
use Tonysm\TailwindCss\DaemonLauncher;
use Tonysm\TailwindCss\Manifest;

class BuildCommand extends Command
{
    protected $signature = '
        tailwindcss:build
        {--watch : If you want to keep the process running to watch your local file changes.}
        {--minify : If you want the final CSS file to be minified.}
        {--digest : If you want the final CSS file to be generated using a digest of its contents (does not work with the --watch flag).}
        {--prod : This option combines the --minify and --digest options. Ideal for production.}
        {--no-tty : Disables TTY output mode. Use this in environments where TTY is not supported or causing issues.}
        {--parent-pid= : Stops the build when this process exits.}
    ';

    protected $description = 'Generates a new build of Tailwind CSS for your project.';

    public function handle(): int
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
        $destinationFileAbsolutePath = $destinationPath.DIRECTORY_SEPARATOR.trim($sourceRelativePath, DIRECTORY_SEPARATOR);
        $destinationFileRelativePath = str_replace(rtrim(public_path(), DIRECTORY_SEPARATOR), '', $destinationFileAbsolutePath);

        File::ensureDirectoryExists(dirname($destinationFileAbsolutePath));
        File::cleanDirectory(dirname($destinationFileAbsolutePath));

        if ($this->option('watch') || ! $this->shouldVersion()) {
            // Ensure there is at least one mix-manifest.json that points to the unversioned asset...
            File::put(Manifest::path(), json_encode([
                $this->fixOsFilePathToUriPath($sourceRelativePath) => $this->fixOsFilePathToUriPath($destinationFileRelativePath),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        $usesTty = ! $this->option('no-tty') && SymfonyProcess::isTtySupported();

        $process = Process::forever()
            ->tty($usesTty)
            // Without a TTY, we hold stdin open. The binary exits when it closes, so it can't outlive us...
            ->when($this->option('watch') && ! $usesTty, fn ($process) => $process->input(new InputStream))
            ->path(base_path());

        $command = array_filter([
            $binFile,
            '-i', $sourcePath,
            '-o', $destinationFileAbsolutePath,
            $this->option('watch') ? ($usesTty ? '--watch=always' : '--watch') : null,
            $this->shouldMinify() ? '-m' : null,
        ]);

        $output = function ($type, $output): void {
            $this->output->write($output);
        };

        if ($parentPid = (int) $this->option('parent-pid')) {
            $this->runUntilParentExits($process->start($command, $output), $parentPid);
        } else {
            $process->run($command, $output);
        }

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

    private function runUntilParentExits(InvokedProcess|FakeInvokedProcess $process, int $parentPid): void
    {
        while ($process->running()) {
            if (! DaemonLauncher::processIsRunning($parentPid)) {
                $process->stop();

                return;
            }

            usleep(100_000);
        }
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
        if ($this->option('digest')) {
            return true;
        }

        return (bool) $this->option('prod');
    }

    private function shouldMinify(): bool
    {
        if ($this->option('minify')) {
            return true;
        }

        return (bool) $this->option('prod');
    }
}
