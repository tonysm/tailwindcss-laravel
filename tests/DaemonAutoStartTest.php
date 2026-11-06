<?php

namespace Tonysm\TailwindCss\Tests;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Foundation\DevCommands;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use ReflectionProperty;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;
use Tonysm\TailwindCss\DaemonLauncher;
use Tonysm\TailwindCss\TailwindCssServiceProvider;

class DaemonAutoStartTest extends TestCase
{
    private FakeDaemonLauncher $launcher;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('tailwindcss.daemon', true);

        $this->app->instance(DaemonLauncher::class, $this->launcher = new FakeDaemonLauncher);
    }

    protected function tearDown(): void
    {
        if (class_exists(DevCommands::class)) {
            $this->clearDevCommands();
        }

        parent::tearDown();
    }

    #[Test]
    public function registers_watcher_as_dev_command(): void
    {
        $this->registerDevCommands();

        $this->assertSame(
            'php artisan tailwindcss:watch --download --no-tty',
            collect(DevCommands::commands())->firstWhere('name', 'tailwindcss')['command'] ?? null,
        );
    }

    #[Test]
    public function doesnt_register_dev_command_when_disabled(): void
    {
        config()->set('tailwindcss.daemon', false);

        $this->registerDevCommands();

        $this->assertNull(collect(DevCommands::commands())->firstWhere('name', 'tailwindcss'));
    }

    #[Test]
    public function launches_watcher_when_serve_starts(): void
    {
        $this->dispatchCommandStarting('serve');

        $this->assertSame([getmypid()], $this->launcher->launches);
    }

    #[Test]
    public function doesnt_launch_for_other_commands(): void
    {
        $this->dispatchCommandStarting('queue:work');

        $this->assertEmpty($this->launcher->launches);
    }

    #[Test]
    public function doesnt_launch_when_disabled(): void
    {
        config()->set('tailwindcss.daemon', false);

        $this->dispatchCommandStarting('serve');

        $this->assertEmpty($this->launcher->launches);
    }

    private function registerDevCommands(): void
    {
        if (! class_exists(DevCommands::class)) {
            $this->markTestSkipped('The dev commands are only available on Laravel 13.16+.');
        }

        $this->clearDevCommands();

        (new TailwindCssServiceProvider($this->app))->packageRegistered();
    }

    private function clearDevCommands(): void
    {
        (new ReflectionProperty(DevCommands::class, 'commands'))->setValue(null, []);
    }

    private function dispatchCommandStarting(string $command): void
    {
        Event::dispatch(new CommandStarting($command, new ArgvInput(['artisan', $command]), new NullOutput));
    }
}

class FakeDaemonLauncher extends DaemonLauncher
{
    public array $launches = [];

    public function launch(int $parentPid): void
    {
        $this->launches[] = $parentPid;
    }
}
