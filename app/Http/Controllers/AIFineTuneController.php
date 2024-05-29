<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\Helper;
use App\Models\Setting;
use App\Models\SettingTwo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orhanerday\OpenAi\OpenAi;

class AIFineTuneController extends Controller
{
    public function addFineTune(Request $request)
    {

        if (Helper::appIsDemo()) {
            return response()->json(__('This feature is disabled in Demo version.'), 419);
        }

        $title = ! empty($_POST['title']) ? $_POST['title'] : 'model-'.Str::random(7);
        $purpose = ! empty($_POST['purpose']) ? $_POST['purpose'] : 'fine-tune';
        $model = ! empty($_POST['model']) ? $_POST['model'] : 'gpt-3.5-turbo-1106';
        $file = ! empty($_FILES['file']) ? $_FILES['file'] : [];

        if (empty($title)) {
            return response()->json(__('Title filed is empty!'), 419);
        }

        if (empty($file['name'])) {
            return response()->json(__('Select a JSONL File!'), 419);
        } else {
            $file_name = basename($file['name']);
            $tmp_file = $file['tmp_name'];
            $file_type = $file['type'];
            $c_file = curl_file_create($tmp_file, $file_type, $file_name);
        }

        // Fetch the Site Settings object with openai_api_secret
        $settings = Setting::first();
        if ($settings?->user_api_option) {
            $apiKeys = explode(',', auth()->user()?->api_keys);
        } else {
            $apiKeys = explode(',', $settings?->openai_api_secret);
        }
        $fine_tune_list = json_decode(SettingTwo::first()->fine_tune_list, true);
        $apiKey = $apiKeys[0];
        $open_ai = new OpenAi($apiKey);
        $html = '';

        // upload file
        $uploadFile = $open_ai->uploadFile([
            'purpose' => $purpose,
            'file' => $c_file,
        ]);
        $uploadFile_array = json_decode($uploadFile, true);
        $uploadFile = json_decode($uploadFile);

        if (isset($uploadFile->error)) {
            return response()->json($uploadFile->error->message, 419);
        }

        // create fine-tune
        $createFineTune = $open_ai->createFineTune([
            'model' => $model,
            'training_file' => $uploadFile->id,
        ]);
        $createFineTune = json_decode($createFineTune);

        if (isset($createFineTune->error)) {
            return response()->json($createFineTune->error, 419);
        }

        // update data
        $fine_tune_list[$uploadFile->id] = [
            'title' => $title,
            'file' => $uploadFile_array,
        ];

        $save_settings = SettingTwo::first();
        $save_settings->fine_tune_list = json_encode($fine_tune_list);
        $save_settings->save();

        return response()->json([
            'output' => sprintf(
                '<tr>
                    <td>%1$s</td>
                    <td>%2$s</td>
                    <td>%3$s</td>
                    <td>%4$s</td>
                    <td>%5$s</td>
                    <td>%6$s</td>
                    <td><button type="button"
                        class="delete-fine-tune btn relative z-10 p-0 border w-[36px] shrink-0 h-[36px] hover:bg-red-600 hover:text-white"
                        title="%7$s">
                        <svg width="10" height="10" viewBox="0 0 10 10"
                            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.08789 1.74609L5.80664 5L9.08789 8.25391L8.26758 9.07422L4.98633 5.82031L1.73242 9.07422L0.912109 8.25391L4.16602 5L0.912109 1.74609L1.73242 0.925781L4.98633 4.17969L8.26758 0.925781L9.08789 1.74609Z" />
                        </svg>
                    </a></td>
                </tr>',
                $title,
                $uploadFile->id,
                $uploadFile->bytes,
                $model,
                $fine_tune->fine_tuned_model ?? '-',
                $createFineTune->status,
                __('Delete')
            ),
        ], 200);

    }

    public function deleteFineTune(Request $request)
    {

        if (Helper::appIsDemo()) {
            return response()->json(__('This feature is disabled in Demo version.'), 419);
        }

        $file_id = ! empty($_POST['file_id']) ? $_POST['file_id'] : '';
        $model = ! empty($_POST['model']) ? $_POST['model'] : '';

        if (empty($file_id) && empty($model)) {
            return response()->json(__('Fine-tune not found!'), 419);
        }

        // Fetch the Site Settings object with openai_api_secret
        $settings = Setting::first();
        if ($settings?->user_api_option) {
            $apiKeys = explode(',', auth()->user()?->api_keys);
        } else {
            $apiKeys = explode(',', $settings?->openai_api_secret);
        }
        $fine_tune_list = json_decode(SettingTwo::first()->fine_tune_list, true);
        $apiKey = $apiKeys[0];
        $open_ai = new OpenAi($apiKey);

        if ($model) {
            $deleteFineTune = $open_ai->deleteFineTune($model);
            $deleteFineTune = json_decode($deleteFineTune);

            if (isset($deleteFineTune->error)) {
                return response()->json($deleteFineTune->error->message, 419);
            }
        }

        $deleteFile = $open_ai->deleteFile($file_id);
        $deleteFile = json_decode($deleteFile);

        if (isset($deleteFile->error)) {
            return response()->json($deleteFile->error->message, 419);
        }

        if (isset($fine_tune_list[$file_id])) {
            unset($fine_tune_list[$file_id]);
            $save_settings = SettingTwo::first();
            $save_settings->fine_tune_list = json_encode($fine_tune_list);
            $save_settings->save();
        }

        return response()->json(__('Fine-tune deleted!'), 200);

    }

    public static function getFineTuneTableRow()
    {

        // Fetch the Site Settings object with openai_api_secret
        $settings = Setting::first();
        if ($settings?->user_api_option) {
            $apiKeys = explode(',', auth()->user()?->api_keys);
        } else {
            $apiKeys = explode(',', $settings?->openai_api_secret);
        }
        $fine_tune_list = json_decode(SettingTwo::first()->fine_tune_list, true);
        $apiKey = $apiKeys[0];
        $open_ai = new OpenAi($apiKey);
        $html = '';

        $listFiles = json_decode($open_ai->listFiles());
        $listFineTunes = json_decode($open_ai->listFineTunes());

        if (empty($fine_tune_list)) {
            return printf('<tr class="info"><td colspan="5">%s</td></tr>', __('There is no fine-tune data!'));
        }

        foreach (array_reverse($fine_tune_list) as $file_id => $values) {
            foreach ($listFineTunes?->data ?? [] as $fine_tune) {
                if ($fine_tune->training_file == $file_id) {
                    $html .= sprintf(
                        '<tr>
                            <td>%1$s</td>
                            <td>%2$s</td>
                            <td>%3$s</td>
                            <td>%4$s</td>
                            <td>%5$s</td>
                            <td>%6$s %7$s</td>
                            <td><button type="button" data-file="%8$s" data-model="%9$s"
                                class="delete-fine-tune btn relative z-10 p-0 border w-[36px] shrink-0 h-[36px] hover:bg-red-600 hover:text-white"
                                title="%10$s">
                                <svg width="10" height="10" viewBox="0 0 10 10"
                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9.08789 1.74609L5.80664 5L9.08789 8.25391L8.26758 9.07422L4.98633 5.82031L1.73242 9.07422L0.912109 8.25391L4.16602 5L0.912109 1.74609L1.73242 0.925781L4.98633 4.17969L8.26758 0.925781L9.08789 1.74609Z" />
                                </svg>
                            </a></td>
                        </tr>',
                        $values['title'],
                        $file_id,
                        $values['file']['bytes'],
                        $fine_tune->model,
                        $fine_tune->fine_tuned_model ?? '-',
                        $fine_tune->error->message ?? '',
                        $fine_tune->status,
                        $file_id,
                        $fine_tune->fine_tuned_model,
                        __('Delete')
                    );
                }
            }
        }

        echo $html;

    }

    public static function getFineModelOption($selected)
    {

        // Fetch the Site Settings object with openai_api_secret
        $settings = Setting::first();
        if ($settings?->user_api_option) {
            $apiKeys = explode(',', auth()->user()?->api_keys);
        } else {
            $apiKeys = explode(',', $settings?->openai_api_secret);
        }
        $fine_tune_list = json_decode(SettingTwo::first()->fine_tune_list, true);
        $apiKey = $apiKeys[0];
        $open_ai = new OpenAi($apiKey);
        $html = '';

        $listFineTunes = json_decode($open_ai->listFineTunes());

        foreach ($listFineTunes?->data ?? [] as $fine_tune) {
            if ($fine_tune->status == 'succeeded' && isset($fine_tune_list[$fine_tune->training_file])) {

                $html .= sprintf(
                    '<option value="%1$s" %3$s>%2$s (%1$s)</option>',
                    $fine_tune->fine_tuned_model,
                    $fine_tune_list[$fine_tune->training_file]['title'],
                    $selected == $fine_tune->fine_tuned_model ? 'selected' : ''
                );
            }
        }

        echo $html;

    }
}
