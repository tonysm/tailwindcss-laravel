<?php

namespace Tonysm\TailwindCss\Tests;

use Illuminate\Http\Request;
use Tonysm\TailwindCss\Http\Middleware\AddLinkHeaderForPreloadedAssets;

class PreloadingHeaderTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function no_link_header_when_not_preloading(): void
    {
        config()->set('tailwindcss.build.manifest_file_path', __DIR__.'/stubs/test-manifest.json');

        $tailwindcss = tailwindcss('css/app.css', preload: false);

        $response = resolve(AddLinkHeaderForPreloadedAssets::class)->handle(
            Request::create('/'),
            fn () => response('hello world'),
        );

        $this->assertEquals('http://localhost/css/app-123.css', (string) $tailwindcss);
        $this->assertEquals('hello world', $response->content());
        $this->assertNull($response->headers->get('Link', null));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function adds_link_header_when_preloading(): void
    {
        config()->set('tailwindcss.build.manifest_file_path', __DIR__.'/stubs/test-manifest.json');

        $tailwindcss = tailwindcss('css/app.css', preload: true);

        $response = resolve(AddLinkHeaderForPreloadedAssets::class)->handle(
            Request::create('/'),
            fn () => response('hello world'),
        );

        $this->assertEquals($asset = 'http://localhost/css/app-123.css', (string) $tailwindcss);
        $this->assertEquals('hello world', $response->content());
        $this->assertEquals("<{$asset}>; rel=preload; as=style", $response->headers->get('Link', null));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function keeps_existing_preloading_link_header(): void
    {
        config()->set('tailwindcss.build.manifest_file_path', __DIR__.'/stubs/test-manifest.json');

        $tailwindcss = tailwindcss('css/app.css', preload: true);

        $response = resolve(AddLinkHeaderForPreloadedAssets::class)->handle(
            Request::create('/'),
            fn () => response('hello world')->withHeaders([
                'Link' => '</js/app.js>; rel=modulepreload',
            ]),
        );

        $this->assertEquals($asset = 'http://localhost/css/app-123.css', (string) $tailwindcss);
        $this->assertEquals('hello world', $response->content());
        $this->assertEquals("</js/app.js>; rel=modulepreload, <{$asset}>; rel=preload; as=style", $response->headers->get('Link', null));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function adds_link_header_when_preloading_custom_attributes(): void
    {
        config()->set('tailwindcss.build.manifest_file_path', __DIR__.'/stubs/test-manifest.json');

        $tailwindcss = tailwindcss('css/app.css', ['crossorigin' => 'anonymous']);

        $response = resolve(AddLinkHeaderForPreloadedAssets::class)->handle(
            Request::create('/'),
            fn () => response('hello world'),
        );

        $this->assertEquals($asset = 'http://localhost/css/app-123.css', (string) $tailwindcss);
        $this->assertEquals('hello world', $response->content());
        $this->assertEquals("<{$asset}>; rel=preload; as=style; crossorigin=anonymous", $response->headers->get('Link', null));
    }
}
