<?php

namespace Tonysm\TailwindCss\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = '
        tailwindcss:install
        {--download : If you also want to download the Tailwind CSS binary.}
    ';

    protected $description = 'Installs the Tailwind CSS scaffolding for new Laravel applications.';

    public function handle()
    {
        $this->warn('Not implemented yet.');

        // Ensure there's a `tailwind.config.js` file (or copy stub if not)
        // Ensure there's a `resources/css/app.css` file (or copy stub if not)

        if ($this->option('download')) {
            $this->call('tailwindcss:download');
        } else {
            $this->info('Done!');
        }

        return self::SUCCESS;
    }
}
