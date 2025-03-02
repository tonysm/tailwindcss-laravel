<?php

namespace Tonysm\TailwindCss\Http\Middleware;

use Tonysm\TailwindCss\Manifest;

class AddLinkHeaderForPreloadedAssets
{
    public function __construct(private readonly Manifest $manifest) {}

    public function handle($request, $next)
    {
        return tap($next($request), function ($response): void {
            if (($assets = $this->manifest->assetsForPreloading()) !== []) {
                $response->header('Link', trim(implode(', ', array_filter(array_merge(
                    [$response->headers->get('Link', null)],
                    collect($assets)->map(fn ($attributes, $asset): string => implode('; ', array_merge(
                        ["<$asset>"],
                        collect(array_merge(['rel' => 'preload', 'as' => 'style'], $attributes))
                            ->map(fn ($value, $key): string => "{$key}={$value}")
                            ->all(),
                    )))->all(),
                )))));
            }
        });
    }
}
