<?php

Route::group(['prefix' => 'install', 'as' => 'LaravelInstaller::', 'namespace' => 'RachidLaasri\LaravelInstaller\Controllers', 'middleware' => ['web', 'install']], function () {
    Route::get('/', [
        'as' => 'welcome',
        'uses' => 'WelcomeController@welcome',
    ])->withoutMiddleware('appstatus');

    Route::get('environment', [
        'as' => 'environment',
        'uses' => 'EnvironmentController@environmentMenu',
    ])->withoutMiddleware('appstatus');

    Route::get('environment/wizard', [
        'as' => 'environmentWizard',
        'uses' => 'EnvironmentController@environmentWizard',
    ])->withoutMiddleware('appstatus');

    Route::post('environment/saveWizard', [
        'as' => 'environmentSaveWizard',
        'uses' => 'EnvironmentController@saveWizard',
    ])->withoutMiddleware('appstatus');

    Route::get('environment/classic', [
        'as' => 'environmentClassic',
        'uses' => 'EnvironmentController@environmentClassic',
    ])->withoutMiddleware('appstatus');

    Route::post('environment/saveClassic', [
        'as' => 'environmentSaveClassic',
        'uses' => 'EnvironmentController@saveClassic',
    ])->withoutMiddleware('appstatus');

    Route::get('requirements', [
        'as' => 'requirements',
        'uses' => 'RequirementsController@requirements',
    ])->withoutMiddleware('appstatus');

    Route::get('permissions', [
        'as' => 'permissions',
        'uses' => 'PermissionsController@permissions',
    ])->withoutMiddleware('appstatus');

    Route::get('database', [
        'as' => 'database',
        'uses' => 'DatabaseController@database',
    ])->withoutMiddleware('appstatus');

    Route::get('final', [
        'as' => 'final',
        'uses' => 'FinalController@finish',
    ])->withoutMiddleware('appstatus');
});

Route::group(['prefix' => 'update', 'as' => 'LaravelUpdater::', 'namespace' => 'RachidLaasri\LaravelInstaller\Controllers', 'middleware' => 'web'], function () {
    Route::group(['middleware' => 'update'], function () {
        Route::get('/', [
            'as' => 'welcome',
            'uses' => 'UpdateController@welcome',
        ])->withoutMiddleware('appstatus');

        Route::get('overview', [
            'as' => 'overview',
            'uses' => 'UpdateController@overview',
        ])->withoutMiddleware('appstatus');

        Route::get('database', [
            'as' => 'database',
            'uses' => 'UpdateController@database',
        ])->withoutMiddleware('appstatus');
    })->withoutMiddleware('appstatus');

    // This needs to be out of the middleware because right after the migration has been
    // run, the middleware sends a 404.
    Route::get('final', [
        'as' => 'final',
        'uses' => 'UpdateController@finish',
    ])->withoutMiddleware('appstatus');
});


Route::group([
    'as' => 'LaravelInstaller::',
    'namespace' => 'RachidLaasri\LaravelInstaller\Controllers',
    'middleware' => ['web'],
    'withoutMiddleware' => ['appstatus']
], function () {
    Route::get('license', [
        'as' => 'license',
        'uses' => 'ApplicationStatusController@license',
    ])->withoutMiddleware('appstatus');

    Route::get('license/upgrade', [
        'as' => 'license.upgrade',
        'uses' => 'ApplicationStatusController@upgrade',
    ])->withoutMiddleware('appstatus');

    Route::post('license', [
        'uses' => 'ApplicationStatusController@licenseCheck',
    ])->withoutMiddleware('appstatus');

    Route::any(
        'api/webhook/license', 'ApplicationStatusController@webhook'
    )->name('license.webhook')
        ->middleware('api')
        ->withoutMiddleware('web')
        ->withoutMiddleware('appstatus');
});

