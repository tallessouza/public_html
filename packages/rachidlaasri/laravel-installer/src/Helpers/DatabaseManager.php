<?php

namespace RachidLaasri\LaravelInstaller\Helpers;

use Exception;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\BufferedOutput;
use App\Models\FrontendSetting;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DatabaseManager
{
    /**
     * Migrate and seed the database.
     *
     * @return array
     */
    public function migrateAndSeed()
    {
        $outputLog = new BufferedOutput;

        $this->sqlite($outputLog);

        return $this->migrate($outputLog);
    }

    /**
     * Run the migration and call the seeder.
     *
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     * @return array
     */
    private function migrate(BufferedOutput $outputLog)
    {
        try {
            Artisan::call('migrate', ['--force'=> true], $outputLog);
            $settings = Setting::first();
            if ($settings == null){
                $settings = new Setting();
                $settings->save();
            }
            $fSettings = FrontendSetting::first();
            if ($fSettings == null){
                $fSettings = new FrontendSetting();
                $fSettings->save();
            }

            $adminUser = User::where('type', 'admin')->first();
            if ($adminUser == null){
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
        } catch (Exception $e) {
            Artisan::call('migrate:reset', ['--force'=> true]);
            return $this->response($e->getMessage(), 'error', $outputLog);
        }

        return $this->seed($outputLog);
    }

    /**
     * Seed the database.
     *
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     * @return array
     */
    private function seed(BufferedOutput $outputLog)
    {
        try {
            Artisan::call('db:seed', ['--force' => true], $outputLog);
        } catch (Exception $e) {
            return $this->response($e->getMessage(), 'error', $outputLog);
        }

        return $this->response(trans('installer_messages.final.finished'), 'success', $outputLog);
    }

    /**
     * Return a formatted error messages.
     *
     * @param string $message
     * @param string $status
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     * @return array
     */
    private function response($message, $status, BufferedOutput $outputLog)
    {
        return [
            'status' => $status,
            'message' => $message,
            'dbOutputLog' => $outputLog->fetch(),
        ];
    }

    /**
     * Check database type. If SQLite, then create the database file.
     *
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     */
    private function sqlite(BufferedOutput $outputLog)
    {
        if (DB::connection() instanceof SQLiteConnection) {
            $database = DB::connection()->getDatabaseName();
            if (! file_exists($database)) {
                touch($database);
                DB::reconnect(Config::get('database.default'));
            }
            $outputLog->write('Using SqlLite database: '.$database, 1);
        }
    }
}
