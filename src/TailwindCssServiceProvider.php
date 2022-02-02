<?php

namespace Tonysm\TailwindCss;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Tonysm\TailwindCss\Commands\TailwindCssCommand;

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
            ->name('tailwindcss-laravel')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_tailwindcss-laravel_table')
            ->hasCommand(TailwindCssCommand::class);
    }
}
