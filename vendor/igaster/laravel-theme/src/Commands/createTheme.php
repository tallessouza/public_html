<?php namespace Igaster\LaravelTheme\Commands;

use Igaster\LaravelTheme\Facades\Theme;

class createTheme extends baseCommand
{
    protected $signature = 'theme:create {themeName?}';
    protected $description = 'Create a new theme';

    public function info($text, $newline = true)
    {
        $this->output->write("<info>$text</info>", $newline);
    }

    public function handle()
    {
        // Get theme name
        $themeName = $this->argument('themeName');
        if (!$themeName) {
            $themeName = $this->ask('Give theme name:');
        }

        // Check that theme doesn't exist
        if ($this->theme_installed($themeName)) {
            $this->error("Error: Theme $themeName already exists");
            return;
        }

        // Read theme paths
        $viewsPath = $this->anticipate("Where will views be located?", [$themeName], $themeName);
        $assetPath = $this->anticipate("Where will assets be located?", [$themeName], $themeName);

        // Calculate Absolute paths & check if they exist
        $viewsPathFull = themes_path($viewsPath, 0755, true);
        $assetPathFull = public_path($assetPath, 0755, true);

        if ($this->files->exists($viewsPathFull)) {
            $this->error("Error: Folder already exists: $viewsPathFull");
            return;
        }

        if ($this->files->exists($assetPathFull)) {
            $this->error("Error: Folder already exists: $assetPathFull");
            return;
        }

        // Ask for parent theme
        $parentTheme = "";
        if ($this->confirm('Extends an other theme?')) {
            $themes = array_map(function ($theme) {
                return $theme->name;
            }, Theme::all());
            $parentTheme = $this->choice('Which one', $themes);
        }

        $customConfiguration = $this->askCustomConfiguration();

        // Display a summary
        $this->info("Summary:");
        $this->info("- Theme name: " . $themeName);
        $this->info("- Views Path: " . $viewsPathFull);
        $this->info("- Asset Path: " . $assetPathFull);
        $this->info("- Extends Theme: " . ($parentTheme ?: "No"));

        if (!empty($customConfiguration)) {
            $this->info("Custom Theme Configuration:");
            foreach ($customConfiguration as $key => $value) {
                $this->info("- $key: " . print_r($value, true));
            }
        }

        if ($this->confirm('Create Theme?', true)) {

            $themeJson = new \Igaster\LaravelTheme\themeManifest(array_merge([
                "name" => $themeName,
                "extends" => $parentTheme,
                "asset-path" => $assetPath,
                // "views-path"  => $viewsPath,
            ], $customConfiguration));

            // Create Paths + copy theme.json
            $this->files->makeDirectory($viewsPathFull);
            $this->files->makeDirectory($assetPathFull);

            $themeJson->saveToFile(themes_path("$viewsPath/theme.json"));

            // Rebuild Themes Cache
            Theme::rebuildCache();
        }
    }

    // You request more information during theme setup. Just override this class and implement
    // the following method. It should return an associative array which will be appended
    // into the 'theme.json' configuration file. You can retreive this values
    // with Theme::getSetting('key') at runtime. You may optionaly want to redifine the
    // command signature too.
    public function askCustomConfiguration()
    {
        return [
            // 'key' => 'value',
        ];
    }

}
