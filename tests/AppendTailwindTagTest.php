<?php

namespace Tonysm\TailwindCss\Tests;

use Tonysm\TailwindCss\Actions\AppendTailwindTag;

class AppendTailwindTagTest extends TestCase
{
    /** @test */
    public function append_tailwind_tag_before_closing_head_tag()
    {
        $contents = <<<'BLADE'
        <html>
            <head>
                <title>Hello World</title>

                @vite(['resources/js/app.js', 'resources/css/app.css'])
            </head>

            <body>
                <!-- ...  -->
            </body>
        </html>
        BLADE;

        $expected = <<<'BLADE'
        <html>
            <head>
                <title>Hello World</title>

                @vite(['resources/js/app.js', 'resources/css/app.css'])

                <!-- TailwindCSS Styles -->
                <link rel="stylesheet" href="{{ tailwindcss('css/app.css') }}" />
            </head>

            <body>
                <!-- ...  -->
            </body>
        </html>
        BLADE;

        $this->assertEquals($expected, (new AppendTailwindTag)($contents));
    }
}
