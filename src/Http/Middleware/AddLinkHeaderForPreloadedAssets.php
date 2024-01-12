<?php

namespace Tonysm\TailwindCss\Http\Middleware;

use Tonysm\TailwindCss\Manifest;

class AddLinkHeaderForPreloadedAssets
{
    public function __construct(private Manifest $manifest)
    {
    }

    public function handle($request, $next)
    {
        return tap($next($request), function ($response) {
            if (count($assets = $this->manifest->assetsForPreloading()) > 0) {
                $response->header('Link', trim(implode(', ', array_filter([
                    $response->headers->get('Link', null),
                    ...collect($assets)
                        ->map(fn ($attributes, $asset) => implode('; ', [
                            "<$asset>",
                            ...collect(array_merge(['rel' => 'preload', 'as' => 'style'], $attributes))
                                ->map(fn ($value, $key) => "{$key}={$value}")
                                ->all(),
                        ]))
                        ->all(),
                ]))));
            }
        });
    }
}
