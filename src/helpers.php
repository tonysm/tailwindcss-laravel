<?php

use Illuminate\Support\HtmlString;
use Tonysm\TailwindCss\Manifest;

if (! function_exists('tailwindcss')) {
    /**
     * Get the path to a versioned TailwindCSS file.
     *
     * @param  bool|array  $preload
     */
    function tailwindcss(string $path, $preload = true): HtmlString|string
    {
        return app(Manifest::class)($path, $preload);
    }
}
