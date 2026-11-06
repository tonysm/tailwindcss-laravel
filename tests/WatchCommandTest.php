<?php

namespace Tonysm\TailwindCss\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\Test;

class WatchCommandTest extends TestCase
{
    private string $directory;

    private string $binPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir().'/tailwindcss-laravel-'.uniqid();
        $this->binPath = $this->directory.'/tailwindcss';

        File::ensureDirectoryExists($this->directory);

        config()->set('tailwindcss.bin_path', $this->binPath);
        config()->set('tailwindcss.build.destination_path', $this->directory.'/dist');
        config()->set('tailwindcss.build.manifest_file_path', $this->directory.'/manifest.json');

        Process::fake();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->directory);

        parent::tearDown();
    }

    #[Test]
    public function downloads_missing_binary_and_builds(): void
    {
        Http::fake(['*' => Http::response('binary')]);

        $this->artisan('tailwindcss:watch', ['--download' => true, '--no-tty' => true])->assertSuccessful();

        $this->assertSame('binary', File::get($this->binPath));
        $this->assertFileDoesNotExist($this->binPath.'.download');
        Process::assertRanTimes(fn () => true, 1);
    }

    #[Test]
    public function doesnt_download_existing_binary(): void
    {
        File::put($this->binPath, 'existing');

        Http::fake();

        $this->artisan('tailwindcss:watch', ['--download' => true, '--no-tty' => true])->assertSuccessful();

        Http::assertNothingSent();
        $this->assertSame('existing', File::get($this->binPath));
        Process::assertRanTimes(fn () => true, 1);
    }

    #[Test]
    public function fails_when_binary_is_missing_without_the_download_flag(): void
    {
        Http::fake();

        $this->artisan('tailwindcss:watch', ['--no-tty' => true])->assertFailed();

        Http::assertNothingSent();
        Process::assertNothingRan();
    }

    #[Test]
    public function failed_download_leaves_no_file_behind_and_doesnt_build(): void
    {
        Http::fake(['*' => Http::response('Not Found', 404)]);

        $this->artisan('tailwindcss:watch', ['--download' => true, '--no-tty' => true])->assertFailed();

        Http::assertSent(fn (Request $request) => str_contains($request->url(), 'tailwindlabs/tailwindcss'));
        $this->assertFileDoesNotExist($this->binPath);
        $this->assertFileDoesNotExist($this->binPath.'.download');
        Process::assertNothingRan();
    }

    #[Test]
    public function failed_forced_download_keeps_the_existing_binary(): void
    {
        File::put($this->binPath, 'existing');

        Http::fake(['*' => Http::response('Not Found', 404)]);

        $this->artisan('tailwindcss:download', ['--force' => true])->assertFailed();

        $this->assertSame('existing', File::get($this->binPath));
    }

    #[Test]
    public function exits_without_building_when_locked_and_parent_is_gone(): void
    {
        File::put($this->binPath, 'existing');

        File::ensureDirectoryExists(dirname($lockPath = storage_path('framework/cache/tailwindcss-watch.lock')));

        $lock = fopen($lockPath, 'c');
        $this->assertTrue(flock($lock, LOCK_EX | LOCK_NB));

        try {
            $this->artisan('tailwindcss:watch', ['--no-tty' => true, '--parent-pid' => $this->deadPid()])->assertSuccessful();
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }

        Process::assertNothingRan();
    }

    #[Test]
    public function holds_stdin_open_when_watching_without_tty(): void
    {
        File::put($this->binPath, 'existing');

        $this->artisan('tailwindcss:watch', ['--no-tty' => true])->assertSuccessful();

        Process::assertRan(fn (PendingProcess $process) => in_array('--watch', $process->command)
            && ! in_array('--watch=always', $process->command)
            && $process->input !== null
            && ! $process->tty);
    }

    #[Test]
    public function starts_the_binary_when_watching_for_a_parent_process(): void
    {
        File::put($this->binPath, 'existing');

        $this->artisan('tailwindcss:watch', ['--no-tty' => true, '--parent-pid' => getmypid()])->assertSuccessful();

        Process::assertRan(fn (PendingProcess $process) => in_array('--watch', $process->command));
    }

    #[Test]
    public function stops_the_binary_when_the_parent_process_is_gone(): void
    {
        File::put($this->binPath, 'existing');

        Process::fake(['*' => Process::describe()->iterations(PHP_INT_MAX)]);

        $this->artisan('tailwindcss:watch', ['--no-tty' => true, '--parent-pid' => $this->deadPid()])->assertSuccessful();

        Process::assertRan(fn (PendingProcess $process) => in_array('--watch', $process->command));
    }

    #[Test]
    public function regular_builds_dont_watch(): void
    {
        File::put($this->binPath, 'existing');

        $this->artisan('tailwindcss:build', ['--no-tty' => true])->assertSuccessful();

        Process::assertRan(fn (PendingProcess $process) => ! in_array('--watch', $process->command)
            && ! in_array('--watch=always', $process->command)
            && $process->input === null);
    }

    private function deadPid(): int
    {
        $process = proc_open([PHP_BINARY, '-r', ''], [], $pipes);
        $pid = proc_get_status($process)['pid'];
        proc_close($process);

        return $pid;
    }
}
