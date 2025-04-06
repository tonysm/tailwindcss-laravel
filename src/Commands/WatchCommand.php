<?php

namespace Tonysm\TailwindCss\Commands;

use Illuminate\Console\Command;

class WatchCommand extends Command
{
    protected $signature = 'tailwindcss:watch
        {--no-tty : Disables TTY output mode. Use this in environments where TTY is not supported or causing issues.}
    ';

    protected $description = 'Generates a new build of Tailwind CSS for your project, and keeps watching your files changes.';

    public function handle()
    {
        return $this->call('tailwindcss:build', [
            '--watch' => true,
            '--no-tty' => $this->option('no-tty'),
        ]);
    }
}
