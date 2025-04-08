# Changelog

All notable changes to `tailwindcss-laravel` will be documented in this file.

## 2.2.1 - 2025-04-08

### What's Changed

* Adds the `no-tty` flag by @gcavanunez in https://github.com/tonysm/tailwindcss-laravel/pull/45

### New Contributors

* @gcavanunez made their first contribution in https://github.com/tonysm/tailwindcss-laravel/pull/45

**Full Changelog**: https://github.com/tonysm/tailwindcss-laravel/compare/2.2.0...2.2.1

## 2.2.0 - 2025-03-31

### What's Changed

* Use the HTTP Client's `sink` method for efficiency https://github.com/tonysm/tailwindcss-laravel/commit/f6f425cd4217817f4f2a258df186fb539b4d8cb1

**Full Changelog**: https://github.com/tonysm/tailwindcss-laravel/compare/2.1.0...2.2.0

## 2.0.0 - 2025-02-12

### What's Changed

- Upgrades to Tailwind V4.

## 1.2.0 - 2025-02-12

### What's Changed

- Fallback to Tailwind V3 in the 1.x tagging. It was my mistake to bump to V4 without a major version. If you switched to V4, use the 2.x tag.

**Full Changelog**: https://github.com/tonysm/tailwindcss-laravel/compare/1.1.0...1.2.0

## 1.1.0 - 2025-01-27

### What's Changed

* Upgrade to Tailwind V4 by @tonysm in https://github.com/tonysm/tailwindcss-laravel/pull/37

**Full Changelog**: https://github.com/tonysm/tailwindcss-laravel/compare/1.0.2...1.1.0

## 0.15.0 - 2024-08-13

### What's Changed

* Bumps Tailwind binary version and tweaks the build command to use the Process facade by @tonysm in https://github.com/tonysm/tailwindcss-laravel/pull/33

**Full Changelog**: https://github.com/tonysm/tailwindcss-laravel/compare/0.14.0...0.15.0

## 0.14.0 - 2024-03-06

### What's Changed

* Laravel 11.x Compatibility by @laravel-shift in https://github.com/tonysm/tailwindcss-laravel/pull/30

### New Contributors

* @laravel-shift made their first contribution in https://github.com/tonysm/tailwindcss-laravel/pull/30

**Full Changelog**: https://github.com/tonysm/tailwindcss-laravel/compare/0.13.1...0.14.0

## 0.13.1 - 2024-02-23

### Changelog

- **CHANGED**: Bump the Tailwind CLI version at https://github.com/tonysm/tailwindcss-laravel/commit/67a0c1ba51bf21daf70f0d9b5a91eb362681f39d
- **CHANGED**: Tweaked the install command to only append the Tailwind link tags to the layout instead of replace the vite directive at https://github.com/tonysm/tailwindcss-laravel/commit/3e7c635e37d52642458505d6601645492566475e

## 0.12.1 - 2024-01-12

### Changelog

- Fix `tailwind.config.js` file requiring plugins that fail

## 0.12.0 - 2024-01-12

### What's Changed

* Add `Link` header by @tonysm in https://github.com/tonysm/tailwindcss-laravel/pull/28

**Full Changelog**: https://github.com/tonysm/tailwindcss-laravel/compare/0.11.0...0.12.0

## 0.11.0 - 2023-11-12

### What's Changed

- Fixes installation command not overriding the default `app.css` file even though it's empty
- Bumps the default TailwindCSS bin version to v3.3.5

**Full Changelog**: https://github.com/tonysm/tailwindcss-laravel/compare/0.10.1...0.11.0

## 0.10.1 - 2023-05-13

### What's changed

- Fixed the TailwindCSS Config JS https://github.com/tonysm/tailwindcss-laravel/commit/37632f77cb1aec72c075f1db8b0cbcf17b03df01
- Bump the Tailwind CLI default version to v3.3.2 https://github.com/tonysm/tailwindcss-laravel/commit/b48c26785cbd02ef6f412f6f76005d28db9b87f3

## 0.10.0 - 2023-05-10

### What's Changed

- Tweaks the install command and tailwind.config stub in https://github.com/tonysm/tailwindcss-laravel/commit/8402efe71545473b9d12f3d846fee994b979cec7

**Full Changelog**: https://github.com/tonysm/tailwindcss-laravel/compare/0.9.0...0.10.0

## 0.9.0 - 2023-02-14

### Changelog

- **CHANGED**: Bump the default Tailwind CLI version to `v3.2.6`
- **CHANGED**: Support for Laravel 10

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
