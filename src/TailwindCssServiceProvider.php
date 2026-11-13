<?php

namespace Tonysm\TailwindCss;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Foundation\DevCommands;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TailwindCssServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('tailwindcss')
            ->hasConfigFile()
            ->hasCommands([
                Commands\DownloadCommand::class,
                Commands\InstallCommand::class,
                Commands\BuildCommand::class,
                Commands\WatchCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->scoped(Manifest::class);
        $this->app->singleton(DaemonLauncher::class);

        // Only available on Laravel 13.16+...
        if (class_exists(DevCommands::class) && config('tailwindcss.daemon')) {
            DevCommands::artisan('tailwindcss:watch --download --no-tty', 'tailwindcss');
        }
    }

    public function packageBooted(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        // When `serve` runs under `php artisan dev`, the watcher's lock makes sure only one of them keeps running...
        Event::listen(function (CommandStarting $event): void {
            if ($event->command === 'serve' && config('tailwindcss.daemon')) {
                $this->app->make(DaemonLauncher::class)->launch(getmypid());
            }
        });
    }
}
