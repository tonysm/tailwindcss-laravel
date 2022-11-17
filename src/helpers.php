<?php

use Illuminate\Support\HtmlString;
use Tonysm\TailwindCss\Manifest;

if (! function_exists('tailwindcss')) {
    /**
     * Get the path to a versioned TailwindCSS file.
     *
     * @param string $path
     * @return \Illuminate\Support\HtmlString|string
     */
    function tailwindcss(string $path): HtmlString|string
    {
        return app(Manifest::class)($path);
    }
}
