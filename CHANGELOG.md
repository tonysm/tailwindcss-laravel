# Changelog

All notable changes to `tailwindcss-laravel` will be documented in this file.

## 0.8.0 - 2022-11-17

### Changelog

- **NEW**: The `tailwindcss()` function throws an exception when the manifest file is missing. When testing, we don't want that behavior as we don't always need to compile our styles to run the tests (sometimes we do). For that reason, you may now use the new `InteractsWithTailwind` trait and call the `$this->withoutTailwind()` method on your tests (or in the base TestCase) to disable the missing manifest exception on your tests. Initial work by @andreasnij (thanks!)

## 0.7.0 - 2022-08-06

### Changelog

- Bumps default Tailwind CLI version to 3.1.8 (https://github.com/tonysm/tailwindcss-laravel/commit/575332c3710d8a1a9beda423740c458ede68402c)

## 0.6.0 - 2022-07-03

### Changelog

- **CHANGED**: The `tailwindcss:install` command was changed to work with the new frontend setup in Laravel 9, which uses Vite instead of Mix. It should also keep working on installs in Laravel apps using Mix. Of course, it also works on apps using neither (**cough* *cough** Importmap Laravel)

## 0.5.1 - 2022-06-27

### Changelog

- Bumps default Tailwind CLI version to 3.1.4 (https://github.com/tonysm/tailwindcss-laravel/commit/a853d4a436d58a554e3cb0e2f878935884dac342)

## 0.5.0 - 2022-06-27

### Changelog

- Changes default scaffolding from using `@tailwind` to using `@import` (https://github.com/tonysm/tailwindcss-laravel/commit/bcc0b9d09cdb375cad59020c670c05b51dae01fa)

## 0.4.1 - 2022-05-01

### Changelog

- **FIXED**: Set the working directory explicitly to `base_path()` by @mucenica-bogdan https://github.com/tonysm/tailwindcss-laravel/pull/12

## 0.4.0 - 2022-04-12

### Changelog

- Adds a `TAILWINDCSS_CLI_VERSION` envvar to allow overriding it without publishing the config file
- Bumps the default CLI version to `v3.0.24`
- Adds a new `--cli-version` option to the `tailwindcss:install` and `tailwindcss:download` commands which may be used by passing the `--cli-version="v3.0.24"`

## 0.3.0 - 2022-02-13

### Changelog

- **CHANGED**: the `tailwindcss.php` config was updated: instead of specifying the destination file path, you define a destination PATH, we'll construct the full file path based on the source file relative path.
- **CHANGED**: The binary destination file in the `tailwindcss.php` now checks if the file destination should end with `.exe` or not (for Windows support)
- **FIXED**: the `tailwindcss:download` and `tailwindcss:build` commands now are working on Windows machines.
- **NEW**: Increase `tailwindcss:download` default timeout when downloading the binary and allow users overriding it with the `--timeout` flag (accepts seconds)


---

**If you have published the config, please, republish it again** (no need to publish it if you haven't done that yet)

## 0.2.0 - 2022-02-13

### Changelog

- **CHANGED**: added a [new config entry for the manifest location path](https://github.com/tonysm/tailwindcss-laravel/blob/main/config/tailwindcss.php#L17). Also, the manifest is now prefixed with a dot. That's because Vapor will include dot files in the `public/` folder by default. I know that's something users can ignore, but that should do it for now. If you have published the `tailwindcss.php` config file, make sure to republish that.

## 0.1.0 - 2022-02-09

### Changelog

- **CHANGED**: Laravel 9 support (nothing changed, just the version constraints)

## 0.0.3 - 2022-02-04

### Changelog

- **FIXED:** the option got renamed from `--production` to `--prod` right before pushing and there were some places still referencing it. That's fixed (https://github.com/tonysm/tailwindcss-laravel/commit/4d8861597442babdd727541d2dcec1bf0ba42f61)

## 0.0.2 - 2022-02-04

### Changelog

- **NEW**: There's now a new `--prod` option for the `tailwindcss:build` command, which combines the `--digest` and `--minify` flags.

## 1.0.0 - 202X-XX-XX

- initial release
