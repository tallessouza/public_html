<?php

use App\Helpers\Classes\InstallationHelper;
use App\Models\OpenAIGenerator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\Theme\ThemeService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

function beforeUpdate(): bool
{
    return true;
}

function afterUpdate(): bool
{

    /*
    Yeni gelen tabloları migrate ediyoruz.
    --force because the environment in production mode, are you sure? diye bir uyarı veriyor bunu atlamak
    */
    Artisan::call('migrate', [
        '--force' => true,
    ]);

	$packagesToRemove = ['pcinaglia/laraupdater', 'rachidlaasri/laravel-installer'];
	foreach ($packagesToRemove as $package) {
		$vendorPackagePath = base_path("vendor/$package");
		$packagePath = base_path("packages/$package");
		if (!is_link($vendorPackagePath)) {
			if (File::exists($vendorPackagePath)) {
				// Check if the path exists and is not a symbolic link. If rachidlaasri/laravel-installer then remove rachidlaasri and etc
				$mainFolder = dirname($vendorPackagePath, 1);
				Log::info("Removing package: $vendorPackagePath");
				File::deleteDirectory($mainFolder);
				Log::info("Package removed: $vendorPackagePath");
				
				// Create symlink if the target directory exists
				if (!file_exists($packagePath)) {
					Log::error("Package not found: $packagePath");
				} else {
					// Ensure the parent directory exists
					$parentDirectory = dirname($vendorPackagePath);
					if (!file_exists($parentDirectory)) {
						mkdir($parentDirectory, 0777, true);
					}
					// Create the symlink
					symlink($packagePath, $vendorPackagePath);
					Log::info("Symlink created: $vendorPackagePath --> $packagePath");
				}
			} else {
				Log::error("Package not found");
			}
		} else {
			Log::error("Package is a symbolic link");
		}
	}
	
    # run installation & seeds
    InstallationHelper::runInstallation();

	# merge theme build styles
	$frontendTheme = setting('front_theme', 'default');
	if($frontendTheme !== 'default'){
		ThemeService::MergeThemeBuild($frontendTheme);
	}
	$dashTheme = setting('dash_theme', 'default');
	if($dashTheme !== 'default'){
		ThemeService::MergeThemeBuild($dashTheme);
	}


	

    return true;
}