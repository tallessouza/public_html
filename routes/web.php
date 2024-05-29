<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InstallationController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\magicaiUpdaterController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use RachidLaasri\LaravelInstaller\Middleware\ApplicationStatus;
use App\Http\Controllers\Common\SitemapController;

Route::get('/test', [TestController::class, 'test']);

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => [ 'checkInstallation', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]], function() {
    Route::get('/', [IndexController::class, 'index'])->name('index');
    
    Route::get('/privacy-policy', [PageController::class, 'pagePrivacy']);
    Route::get('/terms', [PageController::class, 'pageTerms']);


	Route::get('/page/{slug}', [PageController::class, 'pageContent']);
	Route::get('/blog', [BlogController::class, 'index']);
	Route::get('/blog/{slug}', [BlogController::class, 'post']);
	Route::get('/blog/tag/{slug}', [BlogController::class, 'tags']);
	Route::get('/blog/category/{slug}', [BlogController::class, 'categories']);
	Route::get('/blog/author/{slug}', [BlogController::class, 'author']);
});
Route::get('/sitemap.xml', [SitemapController::class, "index"]);

Route::get('/activate', [IndexController::class, 'activate']);

Route::get('/confirm/email/{email_confirmation_code}', [MailController::class, 'emailConfirmationMail']);
// Route::get('/confirm/email/{password_reset_code}', [MailController::class, 'emailPasswordResetEmail']);


//Route::get('/install-script-env-editor', [InstallationController::class, 'envFileEditor'])->name('installer.envEditor');
//Route::post('/install-script-env-editor/save', [InstallationController::class, 'envFileEditorSave'])->name('installer.envEditor.save');
//Route::get('/install-script', [InstallationController::class, 'install'])->name('installer.install');
Route::get('/upgrade-script', [InstallationController::class, 'upgrade'])->withoutMiddleware(
    ApplicationStatus::class
);

Route::get('/update-manual', [InstallationController::class, 'updateManual'])->withoutMiddleware(
    ApplicationStatus::class
);

Route::post('/install-extension/{slug}', [InstallationController::class, 'installExtension']);
Route::post('/uninstall-extension/{slug}', [InstallationController::class, 'uninstallExtension']);


// Clear log file
Route::get('/clear-log', function () {
    $logFile = storage_path('logs/laravel.log');

    if (file_exists($logFile)) {
        unlink($logFile);
    }

    return response()->json(['success' => true]);
});

Route::get('/debug/{token}', function ($token) {
    $storedHash = Config::get('app.debug_hash');
    $hashedToken = Hash::make($token);
    if (Hash::check($token, $storedHash)) {
		$currentDebugValue = env('APP_DEBUG', false);
		$newDebugValue = !$currentDebugValue;
		$envContent = file_get_contents(base_path('.env'));
		$envContent = preg_replace('/^APP_DEBUG=.*/m', "APP_DEBUG=" . ($newDebugValue ? 'true' : 'false'), $envContent);
		file_put_contents(base_path('.env'), $envContent);
		Artisan::call('config:clear');
		return redirect()->back()->with('message', 'Debug mode updated successfully.');
    } else {
        return "Invalid token!";
    }
});
// cache clear
Route::get('/cache-clear', function () {
    try {
        Artisan::call('optimize:clear');
        return response()->json(['success' => true]);
    } catch (\Throwable $th) {
        return response()->json(['success' => false]);
    }
})->name('cache.clear');


Route::get('/check-subscription-end', function () {
    Artisan::call('schedule:run');
    return 'Schedule initiated.';
});
//Updater

Route::get('magicai.updater.check',[magicaiUpdaterController::class, 'check']);
Route::get('magicai.updater.currentVersion', [magicaiUpdaterController::class, 'getCurrentVersion']);
Route::get('magicai.updater.update', [magicaiUpdaterController::class, 'update'])
    ->withoutMiddleware(ApplicationStatus::class)
    ->middleware('admin');


if (file_exists(base_path('routes/custom_routes_web.php'))) {
    include base_path('routes/custom_routes_web.php');
}

require __DIR__.'/auth.php';
require __DIR__.'/panel.php';
require __DIR__.'/webhooks.php';