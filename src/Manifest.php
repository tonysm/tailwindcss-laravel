<?php

namespace Tonysm\TailwindCss;

use Exception;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Manifest
{
    protected array $preloading = [];

    public function assetsForPreloading(): array
    {
        return $this->preloading;
    }

    public static function filename(): string
    {
        return basename(self::path());
    }

    public static function path(): string
    {
        return config('tailwindcss.build.manifest_file_path');
    }

    public function __invoke(string $path, $preload = true): string|\Illuminate\Support\HtmlString
    {
        static $manifests = [];

        if (! Str::startsWith($path, '/')) {
            $path = "/{$path}";
        }

        $manifestPath = static::path();

        if (! isset($manifests[$manifestPath])) {
            if (! is_file($manifestPath)) {
                throw new Exception('The Tailwind CSS manifest does not exist.');
            }

            $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        $manifest = $manifests[$manifestPath];

        if (! isset($manifest[$path])) {
            $exception = new Exception("Unable to locate Tailwind CSS compiled file: {$path}.");

            if (! app('config')->get('app.debug')) {
                report($exception);

                return $path;
            }

            throw $exception;
        }

        $asset = asset($manifest[$path]);

        if ($preload) {
            $this->preloading[$asset] = is_array($preload) ? $preload : [];
        }

        return new HtmlString($asset);
    }
}
