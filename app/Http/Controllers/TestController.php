<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Helpers\Classes\Helper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Services\Ai\Gemini;

class TestController extends Controller
{  
	public function test(){
		Helper::setGeminiKey();
		$model = 'gemini-pro';
		// $history = [
		// 	[
		// 		'parts' => [
		// 			['text' => "Your name is Omar, stick with this name. You are web developer"]
		// 		],
		// 		'role' => 'user'
		// 	],
		// 	[
		// 		'parts' => [
		// 			['text' => "Yes my name is Omar and Iam web developer"]
		// 		],
		// 		'role' => 'model'
		// 	],
		// 	[
		// 		'parts' => [
		// 			['text' => "Who are you? write a long story about yourself."]
		// 		],
		// 		'role' => 'user'
		// 	]
		// ];
		$newhistory = convertHistoryToGemini($history);
		$client = app(Gemini::class);
		$response = $client
			->setHistory($newhistory)
			->stream($model);
		$client->echoStream($response);
	}
	
    public function collectMissingStrings(){
		// Get all translatable strings in the app
		$strings = collect();
		// Replace 'resources' with the actual directory containing your views and files
		$files = File::allFiles(resource_path());
		foreach ($files as $file) {
			$content = file_get_contents($file);
			preg_match_all('/__\((\'|")(.*?)(\'|")\)/', $content, $matches);

			foreach ($matches[2] as $match) {
				$strings->push($match);
			}
		}
		// Load existing translations
		$existingTranslations = json_decode(file_get_contents(base_path('lang/en.json')), true);
		// Add new strings to the translations if the keys do not exist
		foreach ($strings->unique() as $string) {
			if (!isset($existingTranslations[$string])) {
				$existingTranslations[$string] = $string;
			}
		}
		// Write updated translations to en.json
		file_put_contents(base_path('lang/en.json'), json_encode($existingTranslations, JSON_PRETTY_PRINT));
    }
}