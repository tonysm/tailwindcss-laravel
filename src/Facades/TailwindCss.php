<?php

namespace Tonysm\TailwindCss\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Tonysm\TailwindCss\TailwindCss
 */
class TailwindCss extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tailwindcss-laravel';
    }
}
