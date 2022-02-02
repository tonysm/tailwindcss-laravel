<?php

namespace Tonysm\TailwindCss;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Tonysm\TailwindCss\Commands;

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
            ->hasCommand(Commands\DownloadCommand::class)
            ->hasCommand(Commands\InstallCommand::class)
            ->hasCommand(Commands\BuildCommand::class)
            ->hasCommand(Commands\WatchCommand::class)
        ;
    }
}
