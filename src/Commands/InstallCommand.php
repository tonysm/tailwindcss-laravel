<?php

namespace Tonysm\TailwindCss\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Terminal;

class InstallCommand extends Command
{
    protected $signature = '
        tailwindcss:install
        {--download : If you also want to download the Tailwind CSS binary.}
        {--cli-version= : You may override the configured version for the CLI.}
    ';

    protected $description = 'Installs the Tailwind CSS scaffolding for new Laravel applications.';

    private $afterMessages = [];

    public function handle()
    {
        $this->displayHeader('Installing TailwindCSS Laravel', '<bg=blue;fg=black> INFO </>');

        $this->ensureTailwindConfigExists();
        $this->ensureTailwindCliBinaryExists();
        $this->addImportStylesToLayouts();
        $this->addIngoreLines();
        $this->runFirstBuild();

        $this->displayAfterNotes();

        $this->newLine();
        $this->line(' <fg=white>Done!</>');

        return self::SUCCESS;
    }

    private function ensureTailwindConfigExists()
    {
        $this->displayTask('ensuring tailwind.config.js exists', function () {
            $this->copyStubToAppIfMissing(
                stub: __DIR__ . '/../../stubs/tailwind.config.js',
                to: base_path('tailwind.config.js'),
            );

            $this->copyStubToAppIfMissing(
                stub: __DIR__ . '/../../stubs/resources/css/app.css',
                to: resource_path('css/app.css'),
            );

            return self::SUCCESS;
        });
    }

    private function ensureTailwindCliBinaryExists()
    {
        if (! File::exists(config('tailwindcss.bin_path')) || $this->option('download')) {
            $this->displayTask('downloading the Tailwind CLI binary', function () {
                return $this->callSilently('tailwindcss:download', [
                    '--cli-version' => $this->option('cli-version') ?: config('tailwindcss.version'),
                ]);
            });
        }
    }

    private function copyStubToAppIfMissing(string $stub, string $to): void
    {
        if (File::exists($to)) {
            return;
        }

        File::ensureDirectoryExists(dirname($to));
        File::copy($stub, $to);
    }

    private function displayTask($description, $task)
    {
        $width = (new Terminal())->getWidth();
        $dots = max(str_repeat('<fg=gray>.</>', $width - strlen($description) - 13), 0);
        $this->output->write(sprintf('    <fg=white>%s</> %s ', $description, $dots));
        $output = $task();

        if ($output === self::SUCCESS) {
            $this->output->write('<info>DONE</info>');
        } elseif ($output === self::FAILURE) {
            $this->output->write('<error>FAIL</error>');
        } elseif ($output === self::INVALID) {
            $this->output->write('<fg=yellow>WARN</>');
        }

        $this->newLine();
    }

    private function displayHeader($text, $prefix)
    {
        $this->newLine();
        $this->line(sprintf(' %s <fg=white>%s</>  ', $prefix, $text));
        $this->newLine();
    }

    private function addImportStylesToLayouts()
    {
        $this->displayTask('updating layouts', function () {
            if (File::exists(base_path('webpack.mix.js'))) {
                $this->replaceMixStylesToLayouts();
            } elseif (File::exists(base_path('vite.config.js'))) {
                $this->replaceViteStylesToLayouts();
            } else {
                $this->appendTailwindStylesToLayouts();
            }

            return self::SUCCESS;
        });
    }

    private function replaceMixStylesToLayouts()
    {
        $this->existingLayoutFiles()
            ->each(fn ($file) => File::put(
                $file,
                str_replace(
                    "mix('css/app.css')",
                    "tailwindcss('css/app.css')",
                    File::get($file),
                ),
            ));
    }

    private function replaceViteStylesToLayouts()
    {
        $this->existingLayoutFiles()
            ->each(fn ($file) => File::put(
                $file,
                preg_replace(
                    '/\@vite\(\[\'resources\/css\/app.css\', \'resources\/js\/app.js\'\]\)/',
                    "@vite(['resources/js/app.js'])",
                    File::get($file),
                ),
            ));

        $this->appendTailwindStylesToLayouts();
    }

    private function appendTailwindStylesToLayouts()
    {
        $this->existingLayoutFiles()
            ->each(fn ($file) => File::put(
                $file,
                preg_replace(
                    '/(\s*)(<\/head>)/',
                    "\\1    <link rel=\"stylesheet\" href=\"{{ tailwindcss('css/app.css') }}\">\n\\1\\2",
                    File::get($file),
                ),
            ));
    }

    private function existingLayoutFiles()
    {
        return collect(['app', 'guest'])
            ->map(fn ($file) => resource_path("views/layouts/{$file}.blade.php"))
            ->filter(fn ($file) => File::exists($file));
    }

    private function addIngoreLines()
    {
        $this->displayTask('adding ignore lines', function () {
            $binary = basename(config('tailwindcss.bin_path'));

            File::append(base_path('.gitignore'), <<<LINES

            /public/css/
            /public/dist/
            .tailwindcss-manifest.json
            {$binary}
            LINES);

            return self::SUCCESS;
        });
    }

    private function runFirstBuild()
    {
        $this->displayTask('running first build', function () {
            $result = $this->callSilently('tailwindcss:build');

            if ($result !== self::SUCCESS) {
                $this->afterMessages[] = '<fg=white>* Try running `<fg=yellow>php artisan tailwindcss:build</>`</>';

                return self::INVALID;
            }

            return self::SUCCESS;
        });
    }

    private function displayAfterNotes()
    {
        if (count($this->afterMessages) > 0) {
            $this->displayHeader('After Notes & Next Steps', '<bg=yellow;fg=black> NOTES </>');

            foreach ($this->afterMessages as $message) {
                $this->line('    '.$message);
            }
        }
    }
}
