<?php

namespace Tonysm\TailwindCss;

class DaemonLauncher
{
    /** @var resource|null */
    private $process = null;

    /**
     * Starts the watcher in the background. It writes directly to this process'
     * STDOUT and STDERR (no pipes), so it never blocks on a full pipe buffer.
     */
    public function launch(int $parentPid): void
    {
        if ($this->process !== null || ! function_exists('proc_open')) {
            return;
        }

        $process = @proc_open([
            PHP_BINARY,
            $this->consoleScript(),
            'tailwindcss:watch',
            '--download',
            '--no-tty',
            "--parent-pid={$parentPid}",
        ], [
            0 => ['file', PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null', 'r'],
            1 => defined('STDOUT') ? STDOUT : ['file', 'php://stdout', 'w'],
            2 => defined('STDERR') ? STDERR : ['file', 'php://stderr', 'w'],
        ], $pipes, getcwd() ?: base_path());

        $this->process = is_resource($process) ? $process : null;
    }

    public function stop(): void
    {
        if ($this->process === null) {
            return;
        }

        @proc_terminate($this->process);
        @proc_close($this->process);

        $this->process = null;
    }

    public function __destruct()
    {
        $this->stop();
    }

    public static function processIsRunning(int $pid): bool
    {
        // Without the posix extension we can't check, so we assume it's running...
        if (! function_exists('posix_kill')) {
            return true;
        }

        if (@posix_kill($pid, 0)) {
            return true;
        }

        // EPERM means the process exists but belongs to another user...
        return posix_get_last_error() === 1;
    }

    /**
     * The script that started the current process (`artisan`, or `vendor/bin/testbench`
     * when developing packages), so the watcher runs in the same application.
     */
    private function consoleScript(): string
    {
        $script = $_SERVER['SCRIPT_FILENAME'] ?? $_SERVER['argv'][0] ?? null;

        if ($script && is_file($script)) {
            return realpath($script) ?: $script;
        }

        return base_path('artisan');
    }
}
