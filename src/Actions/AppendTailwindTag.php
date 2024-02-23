<?php

namespace Tonysm\TailwindCss\Actions;

class AppendTailwindTag
{
    public function __invoke(string $contents)
    {
        return preg_replace(
            '/(\s*)(<\/head>)/',
            "\n\\1    <!-- TailwindCSS Styles -->".
            "\\1    <link rel=\"stylesheet\" href=\"{{ tailwindcss('css/app.css') }}\" />\\1\\2",
            $contents,
        );
    }
}
