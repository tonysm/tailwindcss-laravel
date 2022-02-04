<?php

namespace Tonysm\TailwindCss;

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
}
