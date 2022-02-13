# Changelog

All notable changes to `tailwindcss-laravel` will be documented in this file.

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
