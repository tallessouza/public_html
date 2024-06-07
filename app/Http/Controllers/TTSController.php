<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\Helper;
use App\Models\OpenAIGenerator;
use App\Models\RateLimit;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\UserOpenai;
use Carbon\Carbon;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\Ai\TTS\AzureService;

class TTSController extends Controller
{
    public function generateSpeech(Request $request)
    {
        $settings_two = SettingTwo::first();
        if ($settings_two->daily_voice_limit_enabled) {
            if (Helper::appIsDemo()) {
                $msg = __('You have reached the maximum number of voice generation allowed on the demo.');
            } else {
                $msg = __('You have reached the maximum number of voice generation allowed.');
            }
            $ipAddress = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : request()->ip();
            $db_ip_address = RateLimit::where('ip_address', $ipAddress)->where('type', 'voice')->first();
            if ($db_ip_address) {
                if (now()->diffInDays(Carbon::parse($db_ip_address->last_attempt_at)->format('Y-m-d')) > 0) {
                    $db_ip_address->attempts = 0;
                }
            } else {
                $db_ip_address = new RateLimit(['ip_address' => $ipAddress]);
            }

            if ($db_ip_address->attempts >= $settings_two->allowed_voice_count) {
                $data = [
                    'errors' => [$msg],
                ];

                return response()->json($data, 429);
            } else {
                $db_ip_address->attempts++;
                $db_ip_address->type = 'voice';
                $db_ip_address->last_attempt_at = now();
                $db_ip_address->save();
            }
        }

        $settings = Setting::first();

        //If speeches are null
        if ($request->speeches == '[]') {
            $data = [
                'errors' => [__('Please provide inputs.')],
            ];

            return response()->json($data, 419);
        }

        $speeches = json_decode($request->speeches, true);

        //Variables and arrays for store
        $wordCount = 0;
        $langsAndVoices = [];

        // Convert the text to SSML format
        // [{"voice":"eu-ES-Standard-A","lang":"eu-ES","content":""},{"voice":"eu-ES-Standard-A","lang":"eu-ES","content":""}]
        //$ssml = '<speak>';

        $resAudio = '';
		// check if one of the speech is azure
		if (in_array('azure', array_column($speeches, 'platform'))) {
			$azureService = new AzureService();
		}else{
			$azureService = null;
		}
		
        foreach ($speeches as $speech) {
            if ($speech['platform'] == 'google') {

                //If gcs credentials are empty
                if (empty($settings->gcs_file) || empty($settings->gcs_name)) {
                    $data = [
                        'errors' => ['Google TTS credentials wrong or missing!'],
                    ];

                    return response()->json($data, 419);
                }

                try {
                    $client = new TextToSpeechClient([
                        'credentials' => storage_path($settings->gcs_file),
                        'project_id' => $settings->gcs_name,
                    ]);
                } catch (\Exception $e) {
                    // Connection error occurred
                    $data = [
                        'errors' => ['Failed to connect to Google TTS service: '.$e->getMessage()],
                    ];

                    return response()->json($data, 419);
                }

                $ssml = '<speak>';
                $ssml .= sprintf(
                    '<lang xml:lang="%3$s">
                        <prosody rate="%4$s">
                            <voice name="%1$s">%2$s</voice>
                            <break time="%5$ss"/>
                        </prosody>
                    </lang>',
                    $speech['voice'],
                    $speech['content'],
                    $speech['lang'],
                    $speech['pace'],
                    $speech['break'],
                );

                $ssml .= '</speak>';

                $langsAndVoices['language'][] = $speech['lang'];
                $langsAndVoices['voices'][] = $speech['voice'];

                // Set the SSML as the synthesis input
                $synthesisInputSsml = (new SynthesisInput())
                    ->setSsml($ssml);

                // Build the voice request, select the language code ("en-US") and the ssml voice gender

                $voice = (new VoiceSelectionParams())
                    ->setLanguageCode('en-US')
                    ->setSsmlGender(SsmlVoiceGender::FEMALE);

                // Effects profile
                // $effectsProfileId = 'telephony-class-application';

                // select the type of audio file you want returned
                $audioConfig = (new AudioConfig())
                    ->setAudioEncoding(AudioEncoding::MP3);
                //->setEffectsProfileId(array($effectsProfileId));

                // Perform text-to-speech request on the SSML input with selected voice parameters and audio file type
                $response = $client->synthesizeSpeech($synthesisInputSsml, $voice, $audioConfig);
                $audioContent = $response->getAudioContent();
                $resAudio = $resAudio.$audioContent;
            } elseif ($speech['platform'] == 'openai') {
                $settings = Setting::first();
                if ($settings?->user_api_option) {
                    $apiKeys = explode(',', auth()->user()?->api_keys);
                } else {
                    $apiKeys = explode(',', $settings?->openai_api_secret);
                }
                $apiKey = $apiKeys[array_rand($apiKeys)];

                //Variables and arrays for store
                $wordCount = 0;
                $langsAndVoices = [];

                $content = $speech['content'];

                $client_ = new Client();

                $langsAndVoices['language'][] = $speech['lang'];
                $langsAndVoices['voices'][] = $speech['voice'];

                Log::info(json_encode([
                    'language' => $speech['language'],
                    'model' => $speech['pace'],
                    'input' => $speech['content'],
                    'voice' => $speech['voice'],
                ]));

                $response = $client_->request('POST', 'https://api.openai.com/v1/audio/speech', [
                    'headers' => [
                        'Authorization' => 'Bearer '.$apiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'language' => $speech['language'],
                        'model' => $speech['pace'],
                        'input' => $speech['content'],
                        'voice' => $speech['voice'],
                    ],
                ]);

                // $tempInputFile = tempnam(sys_get_temp_dir(), 'input_mp3');
                // file_put_contents($tempInputFile, $response->getBody());

                // // Initialize FFMpeg
                // $ffmpeg = FFMpeg::create();

                // // Open the input file
                // $audio = $ffmpeg->open($tempInputFile);

                // // Convert the audio to 128 kbps MP3
                // $format = new Mp3();
                // $format->setAudioKiloBitrate(128);

                // // Save the converted audio to a new file
                // $tempOutputFile = sys_get_temp_dir() . '/output.mp3';
                // $audio->save($format, $tempOutputFile);

                // // Read the converted MP3 data as a string
                // $convertedMp3DataAsString = file_get_contents($tempOutputFile);

                // // Clean up temporary files
                // unlink($tempInputFile);
                // unlink($tempOutputFile);

                $resAudio = $resAudio.$response->getBody();
            } elseif ($speech['platform'] == 'elevenlabs') {
                $settings_two = SettingTwo::first();
                $apiKey = $settings_two->elevenlabs_api_key;

                $content = $speech['content'];

                $client = new Client();

                $response = $client->request('POST', 'https://api.elevenlabs.io/v1/text-to-speech/'.$speech['voice'], [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'xi-api-key' => $apiKey,
                    ],
                    'json' => [
                        'text' => $content,
                        'model_id' => 'eleven_multilingual_v2',
                        'voice_settings' => [
                            'similarity_boost' => 0.75,
                            'stability' => 0.95,
                            'style' => $speech['pace'] / 100,
                            'use_speaker_boost' => true,
                        ],
                    ],
                ]);

                $langsAndVoices['language'][] = $speech['lang'];
                $langsAndVoices['voices'][] = $speech['name'];

                $resAudio = $resAudio.$response->getBody();
            } elseif ($speech['platform'] == 'azure') {
				$voice_id = $speech['voice'];
				$text = $speech['content'];
				$lang = $speech['lang'];
				$langsAndVoices['language'][] = $lang;
                $langsAndVoices['voices'][] = $speech['name'];
				
				$resAudio = $azureService?->synthesizeSpeech($voice_id, $text, $lang);
			}
            $wordCount += countWords($speech['content']);
        }

        $user = Auth::user();
        $ai = OpenAIGenerator::whereSlug('ai_voiceover')->first();

        $audioName = $user->id.'-'.Str::random(20).'.mp3';
        Storage::disk('public')->put($audioName, $resAudio);

		userCreditDecreaseForWord($user, $wordCount);
        if (isset($request->preview)) {
            return response()->json(['output' => '<div class="data-audio" data-audio="/uploads/'.$audioName.'"><div class="audio-preview"></div></div>']);
        }

        //Save in workbook
        $entry = new UserOpenai();
        $entry->team_id = $user->team_id;
        $entry->title = $request->workbook_title;
        $entry->slug = Str::random(20).Str::slug($user->fullName()).'-workbook';
        $entry->user_id = $user->id;
        $entry->openai_id = $ai->id;
        $entry->input = $request->speeches;
        $entry->response = json_encode($langsAndVoices);
        $entry->output = $audioName;
        $entry->hash = Str::random(256);
        $entry->credits = $wordCount;
        $entry->words = $wordCount;
        $entry->save();
        $user->save();
        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $ai->id)->orderBy('created_at', 'desc')->paginate(10);
        $userOpenai->withPath(route('dashboard.user.openai.generator', 'ai_voiceover'));
		$openai = OpenAIGenerator::where('id', $ai->id)->first();
        $html2 = view('panel.user.openai.components.generator_sidebar_table', compact('userOpenai', 'openai'))->render();
        return response()->json(compact('html2'));
    }
}