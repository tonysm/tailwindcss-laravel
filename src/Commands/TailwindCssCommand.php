<?php

namespace Tonysm\TailwindCss\Commands;

use Illuminate\Console\Command;

class TailwindCssCommand extends Command
{
    public $signature = 'tailwindcss-laravel';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
