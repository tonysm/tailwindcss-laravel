<?php

namespace Tonysm\TailwindCss\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tonysm\TailwindCss\DaemonLauncher;

class DaemonLauncherTest extends TestCase
{
    public static function checks(): array
    {
        return [
            'default' => [fn (int $pid) => DaemonLauncher::processIsRunning($pid)],
            'without posix' => [fn (int $pid) => (new ReflectionMethod(DaemonLauncher::class, 'processIsRunningWithoutPosix'))->invoke(null, $pid)],
        ];
    }

    #[Test]
    #[DataProvider('checks')]
    public function detects_running_process(callable $check): void
    {
        $this->assertTrue($check(getmypid()));
    }

    #[Test]
    #[DataProvider('checks')]
    public function detects_exited_process(callable $check): void
    {
        $this->assertFalse($check($this->deadPid()));
    }

    private function deadPid(): int
    {
        $process = proc_open([PHP_BINARY, '-r', ''], [], $pipes);
        $pid = proc_get_status($process)['pid'];
        proc_close($process);

        return $pid;
    }
}
