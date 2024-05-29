<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\Helper;
use App\Helpers\Classes\InstallationHelper;
use App\Models\Setting;
use App\Models\User;
use App\Services\Extension\ExtensionService;
use App\Services\Theme\ThemeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InstallationController extends Controller
{
    public function __construct(
        public ExtensionService $extensionService,
        public ThemeService $themeService
    ) {
    }

    public function envFileEditor()
    {
        try {
            DB::connection()->getPdo();
            $db_set = 1;
        } catch (\Exception $e) {
            $db_set = 2;
        }
        if ($db_set == 1) {
            if (Schema::hasTable('migrations')) {
                $db_ready = 1;
            }
        } else {
            $db_ready = 0;
        }

        if ($db_ready == 0) {
            return view('vendor.installer.env_file_editor');
        }

        if (Auth::check()) {
            if (Auth::user()->type == 'admin') {
                return view('vendor.installer.env_file_editor');
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public function envFileEditorSave(Request $request)
    {

        $envFileData =
            'APP_NAME="'.$request->app_name.'"'."\n".
            'APP_ENV='.$request->environment."\n".
            'APP_KEY='.'base64:'.base64_encode(Str::random(32))."\n".
            'APP_DEBUG='.$request->app_debug."\n".
            'APP_URL='.$request->app_url."\n\n".
            'DB_CONNECTION='.'mysql'."\n".
            'DB_HOST='.$request->database_hostname."\n".
            'DB_PORT='.'3306'."\n".
            'DB_DATABASE='.$request->database_name."\n".
            'DB_USERNAME='.$request->database_username."\n".
            'DB_PASSWORD="'.$request->database_password.'"'."\n\n".
            'BROADCAST_DRIVER='.'log'."\n".
            'CACHE_DRIVER='.'file'."\n".
            'SESSION_DRIVER='.'file'."\n".
            'QUEUE_DRIVER='.'sync'."\n\n".
            'QUEUE_CONNECTION='.'database'."\n\n".
            'REDIS_HOST='.'127.0.0.1'."\n".
            'REDIS_PASSWORD='.'null'."\n".
            'REDIS_PORT='.'6379'."\n\n".
            'MAIL_DRIVER='.$request->mail_driver."\n".
            'MAIL_HOST='.$request->mail_host."\n".
            'MAIL_PORT='.$request->mail_port."\n".
            'MAIL_USERNAME='.$request->mail_username."\n".
            'MAIL_PASSWORD='.$request->mail_password."\n".
            'MAIL_ENCRYPTION='.$request->mail_encryption."\n\n".
            'MAIL_FROM_ADDRESS='.$request->mail_from_address."\n\n".
            'MAIL_FROM_NAME='.$request->mail_from_name."\n\n".
            'PUSHER_APP_ID='.'null'."\n".
            'PUSHER_APP_KEY='.'null'."\n".
            'PUSHER_APP_SECRET='.'null';

        try {
            $envPath = base_path('.env');
            file_put_contents($envPath, $envFileData);
            $request->flash();

            return redirect()->route('installer.install');
        } catch (\Exception $e) {
            echo 'Cannot update .env file. Please update file manually in order to run this script. Need help? <br> <a href="https://liquidthemes.freshdesk.com/support/tickets/new">Submit a Ticket</a>';
        }
    }

    public function install(Request $request)
    {

        try {
            $dbconnect = DB::connection()->getPDO();
            $dbname = DB::connection()->getDatabaseName();
        } catch (\Exception $e) {
            return redirect()->route('installer.envEditor');
        }

        if (! Schema::hasTable('migrations')) {
            Artisan::call('migrate', [
                '--force' => true,
            ]);
            Artisan::call('db:seed', [
                '--force' => true,
            ]);
        } else {
            return redirect()->route('index');
        }

        if (! Schema::hasTable('activity')) {
            return 'You are using Plesk for magicAI. It requires MariaDB 10.X or Mysql 5.6,5.7. Please check your mariaDB or Mysql version. After upgrade your mariadb or mysql please reset the table.';
        }

        //First Startup of Script
        $settings = Setting::first();
        if ($settings == null) {
            $settings = new Setting();
            $settings->save();
        }

        $adminUser = User::where('type', 'admin')->first();
        if ($adminUser == null) {
            $adminUser = new User();
            $adminUser->name = 'Admin';
            $adminUser->surname = 'Admin';
            $adminUser->email = 'admin@admin.com';
            $adminUser->phone = '5555555555';
            $adminUser->type = 'admin';
            $adminUser->password = '$2y$10$XptdAOeFTxl7Yx2KmyfEluWY9Im6wpMIHoJ9V5yB96DgQgTafzzs6';
            $adminUser->status = 1;
            $adminUser->remaining_words = 3000000;
            $adminUser->remaining_images = 3000000;
            $adminUser->affiliate_code = 'P60NPGHAAFGD';
            $adminUser->save();
        }

        Auth::login($adminUser);

        return redirect()->route('dashboard.admin.settings.general');
    }

    public function upgrade()
    {
        $version = 1.15;

        $currentVersion = Setting::first()->script_version;

        if ($version > $currentVersion) {
            if (! Schema::hasTable('migrations')) {
                return 'MagicAI is not installed. Install it first. Go to /install';
            }

            Artisan::call('migrate', [
                '--force' => true,
            ]);

            $settings = Setting::first();
            $settings->script_version = $version;
            $settings->save();

            return "<p>magicAI Updated to the version: $version you can go home. The 1.20 update is for testing. If you want to contribute in this system please go to admin and update menu to test autoupdating system.
<br>This is the last version for updates.
";
        } else {
            return 'Your system is at final version. This method is deprecated please update via admin panel.';
        }
    }

    public function updateManual()
    {
        $version = '6.01';

        /*
        Yeni gelen tabloları migrate ediyoruz.
        --force sebebi ise environmentin productionda olduğunda are you sure? diye bir uyarı veriyor bunu atlamak.
        */
        Artisan::call('migrate', [
            '--force' => true,
        ]);

        # Run the installation
        InstallationHelper::runInstallation();

        File::put(base_path() . '/version.txt', $version);

        $settings = Setting::first();
        $settings->script_version = $version;
        $settings->save();

        return "<p>magicAI Updated to the version: $version. Please don't forget to clear your browser cache. You can close this window.";
    }

    public function installTheme($slug)
    {
        if (Helper::appIsDemo()) {
            return back()
                ->with([
                    'message' => __('This feature is disabled in Demo version.'),
                    'type' => 'error'
                ]);
        }

        try {
            $data = $this->themeService->install($slug);

            if ($data['status']) {
                return redirect()->back()
					->with([
						'message' => $data['message'],
						'type' => 'success'
					]);
            } else {
                return response()
                    ->error(
                        $data['message'],
                        500
                    );
            }
        } catch (Exception $exception) {
            return response()
                ->error(
                    $exception->getMessage(),
                    500
                );
        }
    }

    public function installExtension($slug)
    {
        if (Helper::appIsDemo()) {
            return back()
                ->with([
                    'message' => __('This feature is disabled in Demo version.'),
                    'type' => 'error'
                ]);
        }

        try {
            $data = $this->extensionService->install($slug);

            if ($data['status']) {
                return response()->json($data);
            } else {
                return response()
                    ->error(
                        $data['message'],
                        500
                    );
            }
        } catch (Exception $exception) {
            return response()
                ->error(
                    $exception->getMessage(),
                    500
                );
        }
    }

    public function uninstallExtension($slug)
    {
        if (Helper::appIsDemo()) {
            return back()->with(['message' => __('This feature is disabled in Demo version.'), 'type' => 'error']);
        }

        try {
            $data = $this->extensionService->uninstall($slug);

            if ($data['status']) {
                return response()->json($data);
            } else {
                return response()
                    ->error(
                        $data['message'],
                        500
                    );
            }
        } catch (Exception $exception) {
            return response()
                ->error(
                    $exception->getMessage(),
                    500
                );
        }
    }
}