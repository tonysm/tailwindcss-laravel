<?php

namespace Tonysm\TailwindCss\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;
use Tonysm\TailwindCss\Manifest;
use Tonysm\TailwindCss\TailwindCssServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Tonysm\\TailwindCss\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        if (File::exists($manifestFile = Manifest::path())) {
            File::delete($manifestFile);
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            TailwindCssServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('app.url', 'http://localhost');
        config()->set('app.asset_url', 'http://localhost');
    }
}
