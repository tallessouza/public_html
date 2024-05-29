<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Repositories\Contracts\ExtensionRepositoryInterface;
use App\Repositories\ExtensionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class magicaiUpdaterController extends Controller
{

    private $tmp_backup_dir = null;
    private $response_html = '';


    private function log($msg, $append_response=false, $type='info'){
        //Response HTML
        ini_set('memory_limit', '-1');
        if($append_response)
            $this->response_html .= $msg . "<BR>";
        //Log
        $header = "MagicAI Updater - ";
        if($type == 'info')
            Log::info($header . '[info]' . $msg);
        elseif($type == 'warn')
            Log::error($header . '[warn]' . $msg);
        elseif($type == 'err')
            Log::error($header . '[err]' . $msg);
        else
            return;
    }

    /*
    * Download and Install Update.
    */
    public function update()
    {
        $this->log( trans("magicaiupdater.SYSTEM_VERSION") . $this->getCurrentVersion(), true, 'info' );

        $last_version_info = $this->getLastVersion();
        $last_version = null;

        if ( $last_version_info['version'] <= $this->getCurrentVersion() ){
            $this->log( trans("magicaiupdater.ALREADY_UPDATED"), true, 'info' );
            return;
        }

        try{

            if( ($last_version = $this->download($last_version_info['archive'])) === false){
                return;
            }

            Artisan::call('down'); // Maintenance mode ON
            $this->log( trans("magicaiupdater.MAINTENANCE_MODE_ON"), true, 'info' );
            
            self::backup_en_lang();

            if( ($status = $this->install($last_version)) === false ){
                $this->log( trans("magicaiupdater.INSTALLATION_ERROR"), true, 'err' );
                Artisan::call('up'); // Maintenance mode OFF
                Artisan::call('optimize:clear'); // Clear cache after update
                return;
            }

            self::merge_en_lang();
            
            $this->setCurrentVersion($last_version_info['version']); //update system version
            $this->log( trans("magicaiupdater.INSTALLATION_SUCCESS"), true, 'info' );

            $this->log( trans("magicaiupdater.SYSTEM_VERSION") . $this->getCurrentVersion(), true, 'info' );
            $settings = Setting::first();
            $settings->script_version = $this->getCurrentVersion();
            $settings->save();
            Artisan::call('up'); // Maintenance mode OFF
            Artisan::call('optimize:clear'); // Clear cache after update
            $this->log( trans("magicaiupdater.MAINTENANCE_MODE_OFF"), true, 'info' );

        }catch (\Exception $e) {
            $this->log( trans("magicaiupdater.EXCEPTION") . '<small>' . $e->getMessage() . '</small>', true, 'err' );
            $this->recovery();
        }
    }

    private function backup_en_lang(){
        $backupDirectory  = storage_path('app/lang_backup');
        $backupPath = storage_path('app/lang_backup/en.json');
        if (File::exists(base_path('lang/en.json'))) {

            if (!File::exists($backupDirectory)) {
                File::makeDirectory($backupDirectory, 0755, true, true);
            }
            File::copy(base_path('lang/en.json'), $backupPath);
        } 
    }
    private function merge_en_lang(){
        $backupDirectory  = storage_path('app/lang_backup');
        $langPath = base_path('lang/en.json'); 
        $backupPath = storage_path('app/lang_backup/en.json');

        if (!File::exists($backupDirectory) || !File::exists($backupPath)) {
            return;
        }
        $backupData = json_decode(File::get($backupPath), true);
        $langData = json_decode(File::get($langPath), true);

        if ($backupData !== null && $langData !== null) {
            foreach ($langData as $lang_data_key => $lang_data_value) {
                if (!array_key_exists($lang_data_key, $backupData)) {
                    $backupData[$lang_data_key] = $lang_data_value;
                }
            }
            File::put($langPath, json_encode($backupData, JSON_PRETTY_PRINT));
            File::delete($backupPath);
        }
    }

    private function install($archive)
    {
        try{
            $execute_commands = false;
            $update_script = base_path().'/'.config('magicaiupdater.tmp_folder_name').'/'.config('magicaiupdater.script_filename');

            $zip = new ZipArchive;
            if ($zip->open($archive) === TRUE) {
                $archive = substr($archive, 0, -4);

				$this->checkOutSourceOldVendors();

                $this->log(trans("magicaiupdater.CHANGELOG"), true, 'info');

                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $zip_item = $zip->statIndex($i);
                    $filename = $zip_item['name'];
                    $dirname = dirname($filename);

                    // Exclude files
                    if (substr($filename, -1) == '/' || dirname($filename) === $archive || substr($dirname, 0, 2) === '__') {
                        continue;
                    }

                    // Exclude the version.txt
                    if (strpos($filename, 'version.txt') !== false) {
                        continue;
                    }

                    if (substr($dirname, 0, strlen($archive)) === $archive) {
                        $dirname = substr($dirname, (-1) * (strlen($dirname) - strlen($archive) - 1));
                    }

                    $filename = $dirname . '/' . basename($filename); //set new purify path for current file

                    if (!is_dir(base_path() . '/' . $dirname)) {
                        // Make NEW directory (if it already exists in the current version, continue...)
                        mkdir(base_path() . '/' . $dirname, 0755, true);
                        $this->log(trans("magicaiupdater.DIRECTORY_CREATED") . $dirname, true, 'info');
                    }

                    if (!is_dir(base_path() . '/' . $filename)) {
                        // Overwrite a file with its latest version
                        $contents = $zip->getFromIndex($i);

                        if (strpos($filename, 'upgrade.php') !== false) {
                            file_put_contents($update_script, $contents);
                            $execute_commands = true;
                        } else {
                            if (file_exists(base_path() . '/' . $filename)) {
                                $this->log(trans("magicaiupdater.FILE_EXIST") . $filename, true, 'info');
                                $this->backup($filename); // backup current version
                            }

                            $this->log(trans("magicaiupdater.FILE_COPIED") . $filename, true, 'info');

                            file_put_contents(base_path() . '/' . $filename, $contents, LOCK_EX);
                        }
                    }
                }

                $zip->close();
                echo '</ul>';
            } else {
                return 'Caudlas.';
            }



            if($execute_commands == true){
                require_once($update_script);
                // upgrade-VERSION.php contains the 'main()' method with a BOOL return to check its execution.
                beforeUpdate();
                afterUpdate();
                unlink($update_script);
                $this->log( trans("magicaiupdater.EXECUTE_UPDATE_SCRIPT") . ' (\'upgrade.php\')', true, 'info' );
            }


            File::delete($archive);
            File::deleteDirectory($this->tmp_backup_dir);
            $this->log( trans("magicaiupdater.TEMP_CLEANED"), true, 'info' );

        }catch (\Exception $e) {
            $this->log( trans("magicaiupdater.EXCEPTION") . '<small>' . $e->getMessage() . '</small>', true, 'err' );
            return false;
        }

        return true;
    }

	private function checkOutSourceOldVendors() {
		$packagesToRemove = ['pcinaglia/laraupdater', 'rachidlaasri/laravel-installer'];
		foreach ($packagesToRemove as $package) {
            $packagePath = base_path("vendor/$package");
            if (!is_link($packagePath)) {
               if (File::exists($packagePath)) {
					// Check if the path exists and is not a symbolic link remove the main folder. if rachidlaasri/laravel-installer then remove rachidlaasri and etc
					$mainFolder = dirname($packagePath, 1);
					$this->log("Removing package: $packagePath", true, 'info');
					File::deleteDirectory($mainFolder);
					$this->log("Package removed: $packagePath", true, 'info');
				} else {
					// echo("Package not found: $package");
					$this->log("Package not found", true, 'err');
				}
            } else{
				// echo("Package is a symbolic link: $package");
				$this->log("Package is a symbolic link", true, 'info' );
			}
        }
	}

    private function download($filename) {
        $this->log(trans("magicaiupdater.DOWNLOADING"), true, 'info');

        $tmp_folder_name = base_path() . '/' . config('magicaiupdater.tmp_folder_name');

        if (!is_dir($tmp_folder_name))
            File::makeDirectory($tmp_folder_name, $mode = 0755, true, true);

        try {
            $local_file = $tmp_folder_name . '/' . $filename;
            $remote_file_url = config('magicaiupdater.update_baseurl') . '/' . $filename;
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $remote_file_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $update = curl_exec($curl);
            curl_close($curl);

            File::put($local_file, $update);

        } catch (\Exception $e) {
            $this->log(trans("magicaiupdater.DOWNLOADING_ERROR"), true, 'err');
            $this->log(trans("magicaiupdater.EXCEPTION") . '<small>' . $e->getMessage() . '</small>', true, 'err');
            return false;
        }

        $this->log(trans("magicaiupdater.DOWNLOADING_SUCCESS"), true, 'info');
        return $local_file;
    }

    public function getCurrentVersion() {
        $version = File::get(base_path().'/version.txt');
        return $version;
    }
    private function setCurrentVersion($version) {
        File::put(base_path().'/version.txt', $version);
    }

    public function check() {
        try {
            app(ExtensionRepositoryInterface::class)->request('post', 'request', [])->json();
        }catch (\Exception $e) {}

        $last_version = $this->getLastVersion();

        if( version_compare($last_version['version'], $this->getCurrentVersion(), ">") ) {
            $last_version['update'] = 'yes'; // Trigger the new version available.
            $last_version['version_format'] = format_double($last_version['version']);
            return $last_version;
        }

        return $last_version; // Always return the json because of changelog data.
    }


    private function getLastVersion() {
        $curl = curl_init();
        $url = 'https://api.liquid-themes.com/magicai/updater-v2' . '/magicaiupdater.json';

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $last_version = curl_exec($curl);
        curl_close($curl);

        $last_version = json_decode($last_version, true);
        // $last_version: ['version' => $v, 'archive' => 'RELEASE-$v.zip', 'description' => 'plainText'];
        return $last_version;
    }


    private function backup($filename) {
        if( !isset($this->tmp_backup_dir) )
            $this->tmp_backup_dir = base_path().'/backup_'.date('Ymd');

        $backup_dir = $this->tmp_backup_dir;
        if ( !is_dir($backup_dir) )
            File::makeDirectory($backup_dir, $mode = 0755, true, true);

        if ( !is_dir($backup_dir.'/'.dirname($filename)) )
            File::makeDirectory($backup_dir.'/'.dirname($filename), $mode = 0755, true, true);

        File::copy(base_path().'/'.$filename, $backup_dir.'/'.$filename); //to backup folder
    }

    private function recovery(){
        $this->log( trans("magicaiupdater.RECOVERY") . '<small>' . $e . '</small>', true, 'info' );

        if( !isset($this->tmp_backup_dir) ){
            $this->tmp_backup_dir = base_path().'/backup_'.date('Ymd');
            $this->log( trans("magicaiupdater.BACKUP_FOUND") . '<small>' . $this->tmp_backup_dir . '</small>', true, 'info' );
        }

        try{
            $backup_dir = $this->tmp_backup_dir;
            $backup_files = File::allFiles($backup_dir);
            foreach ($backup_files as $file){
                $filename = (string)$file;
                $filename = substr($filename, (strlen($filename)-strlen($backup_dir)-1)*(-1));
                File::copy($backup_dir.'/'.$filename, base_path().'/'.$filename); //to respective folder
            }

        }catch(\Exception $e) {
            $this->log( trans("magicaiupdater.RECOVERY_ERROR"), true, 'err' );
            $this->log( trans("magicaiupdater.EXCEPTION") . '<small>' . $e->getMessage() . '</small>', true, 'err' );
            return false;
        }

        $this->log( trans("magicaiupdater.RECOVERY_SUCCESS"), true, 'info' );
        return true;
    }


}