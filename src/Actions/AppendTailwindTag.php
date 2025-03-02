<?php

namespace Tonysm\TailwindCss\Actions;

class AppendTailwindTag
{
    public function __invoke(string $contents): ?string
    {
        if (str_contains($contents, '{{ tailwindcss(')) {
            return $contents;
        }

        return preg_replace(
            '/(\s*)(<\/head>)/',
            PHP_EOL.'\\1    <!-- TailwindCSS Styles -->'.
            "\\1    <link rel=\"stylesheet\" href=\"{{ tailwindcss('css/app.css') }}\" />\\1\\2",
            $contents,
        );
    }
}
