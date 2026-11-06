<?php

namespace Tonysm\TailwindCss\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Tonysm\TailwindCss\DaemonLauncher;

class WatchCommand extends Command
{
    protected $signature = 'tailwindcss:watch
        {--no-tty : Disables TTY output mode. Use this in environments where TTY is not supported or causing issues.}
        {--download : Downloads the Tailwind CSS binary if it is missing.}
        {--parent-pid= : Stops watching when this process exits.}
    ';

    protected $description = 'Generates a new build of Tailwind CSS for your project, and keeps watching your files changes.';

    /** @var resource|null */
    private $lock = null;

    public function handle()
    {
        if (! $this->acquireLock()) {
            return self::SUCCESS;
        }

        try {
            if ($this->option('download') && ! File::exists(config('tailwindcss.bin_path'))) {
                if ($this->call('tailwindcss:download') !== self::SUCCESS) {
                    return self::FAILURE;
                }
            }

            return $this->call('tailwindcss:build', [
                '--watch' => true,
                '--no-tty' => $this->option('no-tty'),
                '--parent-pid' => $this->option('parent-pid'),
            ]);
        } finally {
            flock($this->lock, LOCK_UN);
        }
    }

    /**
     * Only one watcher may run per application (`php artisan dev` starts one as its own process,
     * and its `serve` process tries to start another). The ones that don't get the lock wait,
     * so they take over if the running watcher goes away. This must come before the build
     * because it cleans the destination directory and rewrites the manifest file.
     */
    private function acquireLock(): bool
    {
        File::ensureDirectoryExists(dirname($path = storage_path('framework/cache/tailwindcss-watch.lock')));

        // The "e" flag (close-on-exec) prevents the binary from inheriting the lock...
        $this->lock = fopen($path, 'ce');

        $waiting = false;
        $parentPid = (int) $this->option('parent-pid');

        while (! flock($this->lock, LOCK_EX | LOCK_NB)) {
            if (! $waiting) {
                $this->info('Another Tailwind CSS watcher is already running for this application. Waiting for it to stop...');
                $waiting = true;
            }

            if ($parentPid > 0 && ! DaemonLauncher::processIsRunning($parentPid)) {
                return false;
            }

            sleep(1);
        }

        return true;
    }
}
