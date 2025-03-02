<?php

namespace Tonysm\TailwindCss\Testing;

use Illuminate\Support\HtmlString;
use Tonysm\TailwindCss\Manifest;

trait InteractsWithTailwind
{
    /**
     * Register an empty handler for TailwindCSS Laravel Manifest in the container.
     */
    protected function withoutTailwind(): static
    {
        $this->swap(Manifest::class, fn(): \Illuminate\Support\HtmlString => new HtmlString(''));

        return $this;
    }
}
