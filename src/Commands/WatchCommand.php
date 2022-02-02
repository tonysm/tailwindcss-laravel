<?php

namespace Tonysm\TailwindCss\Commands;

use Illuminate\Console\Command;

class WatchCommand extends Command
{
    protected $signature = 'tailwindcss:watch';
    protected $description = 'Generates a new build of Tailwind CSS for your project, and keeps watching your files changes.';

    public function handle()
    {
        return $this->call('tailwindcss:build', [
            '--watch' => true,
        ]);
    }
}
