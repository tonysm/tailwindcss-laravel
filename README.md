<h1 align="center">Tailwind CSS for Laravel</h1>

<p align="center">
    <a href="https://packagist.org/packages/tonysm/tailwindcss-laravel">
        <img src="https://img.shields.io/packagist/dt/tonysm/tailwindcss-laravel.svg?style=flat-square" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/tonysm/tailwindcss-laravel">
        <img src="https://img.shields.io/packagist/v/tonysm/tailwindcss-laravel" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/tonysm/tailwindcss-laravel">
        <img src="https://img.shields.io/packagist/l/tonysm/tailwindcss-laravel" alt="License">
    </a>
</p>

## Introduction

This package wraps the standalone [Tailwind CSS CLI tool](https://tailwindcss.com/blog/standalone-cli). No Node.js required.

### Inspiration

This package was inspired by the [Tailwind CSS for Rails](https://github.com/rails/tailwindcss-rails) gem.

## Installation

You can install the package via composer:

```bash
composer require tonysm/tailwindcss-laravel
```

Optionally, you can publish the config file with:

```bash
php artisan vendor:publish --tag="tailwindcss-config"
```

## Usage

The package consists of 4 commands and a helper function.

### Download the Tailwind CSS Standalone Binary

Since each OS/CPU needs its own version of the compiled binary, the first thing you need to do is run the download command:

```bash
php artisan tailwindcss:download
```

This will detect the correct version based on your OS and CPU architecture.

By default, it will place the binary at the root of your app. The binary will be called `tailwindcss`. You may want to add that line to your project's `.gitignore` file.

Alternatively, you may configure the location of this binary file in the `config/tailwindcss.php` (make sure you export the config file if you want to do that).

### Installing the Scaffolding

There are some files needed for the setup to work. On a fresh Laravel application, you may run the install command, like so:

```bash
php artisan tailwindcss:install
```

This will ensure there's a `tailwind.config.js` file at the root of your project, as well as a `resources/css/app.css` file with the basic Tailwind CSS setup.

### Building

To build the Tailwind CSS styles, you may use the build command:

```bash
php artisan tailwindcss:build
```

By default, that will read your `resources/css/app.css` file and generate the compiled CSS file at `public/css/app.css`.

You may want to generate the final CSS file with a digest on the file name for cache busting reasons (ideal for production). You may do so with the `--digest` flag:

```bash
php artisan tailwindcss:build --digest
```

You may also want to generate a minified version of the final CSS file (ideal for production). You may do so with the `--minify` flag:

```bash
php artisan tailwindcss:build --minify
```

These two flags will make the ideal production combo. Alternatively, you may prefer using a single `--prod` flag instead, which is essentially the same thing, but shorter:

```bash
php artisan tailwindcss:build --prod
```

### Watching For File Changes

When developing locally, it's handy to run the watch command, which will keep an eye on your local files and run a build again whenever you make a change locally:

```bash
php artisan tailwindcss:watch
```

_Note: the watch command is not meant to be used in combination with `--digest` or `--minify` flags._

### Using the Compiled Asset

To use the compiled asset, you may use the `tailwindcss` helper function instead of the `mix` function like so:

```diff
- <link rel="stylesheet" href="{{ mix('css/app.css') }}" >
+ <link rel="stylesheet" href="{{ tailwindcss('css/app.css') }}" >
```

That should be all you need.

### Deploying Your App

When deploying the app, make sure you add the ideal build combo to your deploy script:

```bash
php artisan tailwindcss:build --prod
```

If you're running on a "fresh" app (or an isolated environment, like Vapor), and you have added the binary to your `.gitignore` file, make sure you also add the download command to your deploy script before the build one. In these environments, your deploy script should have these two lines

```bash
php artisan tailwindcss:download
php artisan tailwindcss:build --prod
```

### Mock Manifest When Testing

The `tailwindcss()` function will throw an exception when the manifest file is missing. However, we don't always need the manifest file when running our tests. You may use the `InteractsWithTailwind` trait in your main TestCase to disable that exception throwing:

```php
<?php

namespace Tests;

use Exception;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tonysm\TailwindCss\Testing\InteractsWithTailwind;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use InteractsWithTailwind;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutTailwind();
    }
}
```

Alternatively, you may also use the trait on specific test cases if you want to, so we can toggle that behavior as you need:

```php
<?php

namespace Tests;

use Exception;
use Tests\TestCase;
use Tonysm\TailwindCss\Testing\InteractsWithTailwind;

class ExampleTest extends TestCase
{
    use InteractsWithTailwind;

    /** @test */
    public function throws_exception_when_manifest_is_missing()
    {
        $this->expectException(Exception::class);

        $this->withoutExceptionHandling()
            ->get(route('login'));

        $this->fail('Expected exception to be thrown, but it did not.');
    }

    /** @test */
    public function can_disable_tailwindcss_exception()
    {
        $this->withoutTailwind()
            ->get(route('login'))
            ->assertOk();
    }
}
```

Both tests should pass.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Tony Messias](https://github.com/tonysm)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
