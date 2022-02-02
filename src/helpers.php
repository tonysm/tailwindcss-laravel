<?php

use Tonysm\TailwindCss\Manifest;

if (! function_exists('tailwindcss')) {
    function tailwindcss(string $path): string
    {
        return asset((new Manifest())($path));
    }
}
