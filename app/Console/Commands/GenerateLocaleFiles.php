<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateLocaleFiles extends Command
{
    protected $signature = 'locales:generate';
    protected $description = 'Generate initial locale files structure';

    public function handle()
    {
        $locales = ['pt_BR', 'en', 'es'];
        $files = ['site', 'auth', 'validation'];

        foreach ($locales as $locale) {
            $path = lang_path($locale);
            
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            foreach ($files as $file) {
                $filePath = "{$path}/{$file}.php";
                
                if (!File::exists($filePath)) {
                    File::put($filePath, "<?php\n\nreturn [\n    // Translations for {$file}\n];");
                    $this->info("Created: {$filePath}");
                }
            }
        }

        $this->info('Locale files structure generated successfully!');
    }
}