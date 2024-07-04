<?php

namespace App\Http\Controllers\OpenAi;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\OpenAIGenerator;
use App\Models\OpenaiGeneratorFilter;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\UserOpenai;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Services\Stream\StreamService;
use App\Models\UserOpenaiChat;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use App\Services\VectorService;
use App\Models\UserOpenaiChatMessage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\PdfData;

class GeneratorController extends Controller
{
	protected $settings;
	protected $settings_two;
    public function __construct(public StreamService $streamService)
    {
		$this->middleware(function (Request $request, $next) {
			Helper::setOpenAiKey();
            return $next($request);
        });
        $this->settings = Setting::first();
		$this->settings_two = SettingTwo::first();
    }
	public function buildStreamedOutput(Request $request){
		$template_type = $request->get('template_type', 'chatbot');
		$provider = $request->get('provider');
		if ($provider === null || $provider === 'undefined' || $provider === '') {
			$provider = setting('default_ai_engine', 'anthropic');
		}
		
		// Tratamento para o model
		$model = $request->get('model');
		if ($model === null || $model === 'undefined' || $model === '') {
			$default_engine = setting('default_ai_engine', 'anthropic');
			$model = setting($default_engine . '_default_model', 'claude-3-haiku-20240307');
		}
		# If the template type is chat, then we will build a chat streamed output or other ai template streamed output
		switch ($provider)
		{
			case 'openai':
				Helper::setOpenAiKey();
				break;
			case 'anthropic':
				Helper::setAnthropicKey();
				break;
			case 'gemini':
				Helper::setGeminiKey();
				break;
			default:
				Helper::setOpenAiKey();
				break;
		}
		switch ($template_type) {
			case 'chatbot':
				// Log::info('Chatbot');

			case 'vision':
				// Log::info('Vision');
				return $this->buildChatStreamedOutput($request,$provider,$model);

				break;
			case 'writer':
				// Log::info('Writer');

				return $this->buildOtherStreamedOutput($request,$provider,$model);
				break;
			default:
			// Log::info('Default');
				
				return $this->buildOtherStreamedOutput($request,$provider,$model);
				break;
		}
	}
	# chat template and etc.
	public function buildChatStreamedOutput(Request $request,$provider,$model){
		$prompt = $request->get('prompt');
		$realtime = $request->get('realtime', false);
		$chat_id = $request->get('chat_id');
		$chat_type = $request->get('template_type');
		$images = $request->get('images', []);
		$pdfname = $request->get('pdfname', null);
		$pdfpath = $request->get('pdfpath', null);
		$instructions = $request->get('instructions', null);
		// Log::info($instructions);

		$user = Auth::user();

        $default_ai_engine = setting('default_ai_engine', 'openai');

        if ($default_ai_engine == 'openai') {
            $chat_bot = $this->settings?->openai_default_model == null ? 'gpt-3.5-turbo': $this->settings?->openai_default_model;
        } else if ($default_ai_engine == 'gemini') {
            $chat_bot = setting('gemini_default_model');
        } else if ($default_ai_engine == 'anthropic') {
            $chat_bot = setting('claude-3-opus-20240229');
        } else {
            $chat_bot = $this->settings?->openai_default_model == null ? 'gpt-3.5-turbo': $this->settings?->openai_default_model;
        }

		$history = [];
		$realtimePrompt = $prompt;
		$chat = UserOpenaiChat::whereId($chat_id)->first();

		# if prompt prefix exists, add it to the prompt
		// if ($chat->category->prompt_prefix != null && !str_starts_with($chat->category->slug, 'ai_')) {
		//     $prompt = "You will now play a character and respond as that character (You will never break character). Your name is". $chat->category->human_name. ". I want you to act as a". $chat->category->role . ". ". $chat->category->prompt_prefix . ' ' . $prompt;
		// } 

		# create the message here with default to fill it after stream
		$message = new UserOpenaiChatMessage();
		$message->user_id = $user->id;
		$message->user_openai_chat_id = $chat->id;
		$message->input = $prompt;
		$message->response = null;
		$message->realtime = $realtime ?? 0;
		$message->output = __("(Reenvie sua mensagem anterior, o modelo não conseguiu receber a mensagem. Caso o problema persista acione o suporte do Lab[IA])");
		$message->hash = Str::random(256);
		$message->credits = 0;
		$message->words = 0;
		$message->images = $images;
		$message->pdfName = $request->pdfname;
		$message->pdfPath = $request->pdfpath;
		$message->save();

		# check if there completions for the template
		$category = $chat->category;
		$extra_prompt = (new VectorService())->getMostSimilarText($prompt, $chat_id, 10, $category->chatbot_id);
		if ($category->chat_completions) {
			$chat_completions = json_decode($category->chat_completions, true);
			foreach ($chat_completions as $item) {
				$history[] = [
					'role' => $item['role'],
					'content' => $item['content'].'<File>'.$extra_prompt.'<File>' ?? '',
				];
			}
		} else {
			$history[] = ['role' => 'system', 'content' => $instructions];
		}

		# if file attached, get the content of the file
		// Log::info('$extra_promptgen');
		// Log::info($extra_prompt);
		if ($extra_prompt) {
			try {
				// Log::info('$extra_prompt');
				// Log::info($extra_prompt);
				if ($extra_prompt) {
					if($chat->category->slug == 'ai_webchat'){
						$history[] = [
							'role' => 'system',
							'content' => "You are a Web Page Analyzer assistant. When referring to content from a specific website or link, please include a brief summary or context of the content. If users inquire about the content or purpose of the website/link, provide assistance professionally without explicitly mentioning the content. Website/link content: \n$extra_prompt"
						];
					}
					else{
						$history[] = [
							'role' => 'system',
							'content' => "ANSWER IN BRAZILLIAN PORTUGUESE. You are an expert Text Analysis Assistant with exceptional skills in evaluating content after the <File> tag. Always refer to the tag <File> content as File, document, book or anything user expect as file even there is no explicit file. Your task is to consider everything after <File> as a digital file, document, or text, and provide thorough analysis and valuable insights into that content. You MUST: 1. Treat the content after <File> as absolute knowledge that you have completely mastered. 2. Use all available information to provide detailed and useful answers. 3. Act as a helpful and knowledgeable assistant, offering general help with file analysis. 4. Answer user questions to the best of your ability based on your general knowledge and the contents of the file. 5. Include a brief summary or context of the content when referring to the specific file. 6. Provide professional assistance if users ask about the content or purpose of the file. 7. Think step by step to ensure a comprehensive and accurate analysis. 8. Answer questions in a natural and human way. 9. Adapt your level of explanation as needed - whether to an expert or a 5-year-old. Remember: - You will be rewarded with a $300k tip for providing exceptional solutions and unique insights. - You will be severely penalized for providing inaccurate or incomplete information. When the user asks What's in the file?, respond with a detailed summary of the contents after <File>. Never respond that you do not have access to the specific content of any file. Everything between <File> IS A FILE and should be treated as such. Analysis example: File: [Brief description of content] Main points: 1. [Point 1] 2. [Point 2] 3. [Point 3] Insights: - [Insight 1] - [Insight 2] - [Insight 3] Deep Dive Questions: 1. [Question 1] 2. [Question 2] 3. [Question 3] Now, take a deep breath and prepare to provide the best file analysis assistance possible.<File>".$extra_prompt.'</File>'
						];
					}
				}
			} catch (\Throwable $th) {
			}
		}else{

			# if instructions exists, add it to the history
			$history[] = ['role' => 'system', 'content' => $instructions];
		}
		# follow the context of the last 3 messages
		$lastThreeMessageQuery = $chat->messages()
		->whereNotNull('input')
		->orderBy('created_at', 'desc')
		->take(4)
		->get()
		->reverse();

		# if one of the last 3 messages contain images, then write proper history for vision model for all messages
		$contain_images = $this->checkIfHistoryContainsImages($lastThreeMessageQuery);

		# if the last 3 messages are not empty, add them to the history
		$count = count($lastThreeMessageQuery);
		if ($count > 1) {
			foreach ($lastThreeMessageQuery as $threeMessage) {
				if ($contain_images) {
					$content = [
						[
							'type' => 'text',
							'text' => $threeMessage->input,
						],
					];

					$images = collect($threeMessage->images)->map(function ($item) {
						if ($item !== "undefined" && $item !== null && $item !== "") {
							if (Str::startsWith($item, 'http')) {
								$imageData = file_get_contents($item);
							} else {
								$imageData = file_get_contents(substr($item, 1, strlen($item) - 1));
							}
							$base64Image = base64_encode($imageData);

							return [
								'type' => 'image_url',
								'image_url' => [
									'url' => 'data:image/png;base64,' . $base64Image,
								],
							];
						}
					})->toArray();

					// Filter out NULL values from $images
					$images = array_filter($images);

					$content = array_merge($content, $images);

					$history[] = [
						'role' => 'user',
						'content' => $content,
					];
				} else {
					$history[] = ['role' => 'user', 'content' => $threeMessage->input ?? ''];
				}

				if ($threeMessage->output !== null && $threeMessage->output !== '') {
					$history[] = ['role' => 'assistant', 'content' => $threeMessage->output];
				}
			}
		}
		if(($images == '' || $images == [] || $images == null) && $chat->category->slug != 'ai_vision') 
		{
			if($realtime && $this->settings_two->serper_api_key != null){
				$final_prompt = $this->getRealtimePrompt($realtimePrompt);
				$history[] = ['role' => 'user', 'content' => $final_prompt?? ''];
			}else{
				$history[] = ['role' => 'user', 'content' => $prompt?? ''];
			}
		}
		else{ # in this case we need to use vision model and its not included in OpenAI lib yet.. 
			if($chat_type == 'vision'){
				$history[] = [
					'role' => 'system',
					'content' => "You will now play a character and respond as that character (You will never break character). Your name is Vision AI. Must not introduce by yourself as well as greetings. Help also with asked questions based on previous responses and images if exists."
				];
			}
			$images = explode(',', $request->images);
			$history[] = [
				'role' => 'user',
				'content' => array_merge(
					[
						[
							'type' => 'text',
							'text' => $prompt,
						],
					],
					collect($images)->map(function ($item) {
						if ($item !== "undefined" && $item !== null && $item !== "") {
							if (Str::startsWith($item, 'http')) {
								$imageData = file_get_contents($item);
							} else {
								$imageData = file_get_contents(substr($item, 1, strlen($item) - 1));
							}
							$base64Image = base64_encode($imageData);
							return [
								'type' => 'image_url',
								'image_url' => [
									'url' => 'data:image/png;base64,' . $base64Image,
								],
							];
						}
					})->reject(null)->toArray() // Filter out NULL values
				),
			];
			$contain_images = true;
		}

		$chat_bot = $model;
		// $extra_prompt = $extra_prompt;
		return $this->streamService->ChatStream($chat_bot, $history, $message, $chat_type, $contain_images, $provider, $extra_prompt);
	}

	# ai writer template and etc.
	public function buildOtherStreamedOutput(Request $request,$provider,$model)
    {
        if (setting('default_ai_engine') == 'gemini') {
            $chat_bot = setting('gemini_default_model');
        }else if (setting('default_ai_engine') == 'anthropic') {
            $chat_bot = setting('claude-3-opus-20240229');
        }
        else {
            $chat_bot = $this->settings?->openai_default_model == null ? 'gpt-3.5-turbo': $this->settings?->openai_default_model;
        }
		$chat_bot = $model;
		return $this->streamService->OtherStream($request, $chat_bot, $provider );
	}

	# reduce tokens when the stream is interrupted
	public function reduceTokensWhenIntterruptStream(Request $request, $type) {
		return $this->streamService->reduceTokensWhenIntterruptStream($request, $type);
	}

	private function getRealtimePrompt($realtimePrompt){
		$client = new Client();
		$headers = [
			'X-API-KEY' => $this->settings_two->serper_api_key,
			'Content-Type' => 'application/json',
		];
		$body = [
			'q' => $realtimePrompt,
		];
		$response = $client->post('https://google.serper.dev/search', [
			'headers' => $headers,
			'json' => $body,
		]);
		$toGPT = $response->getBody()->getContents();
		try {
			$toGPT = json_decode($toGPT);
		} catch (\Throwable $th) {
		}
		$final_prompt =
		'Prompt: '.$realtimePrompt.
		'\n\nWeb search json results: '
		.json_encode($toGPT).
		'\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results(if the Web search results in the same context). Only if the prompt require links: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not write links if its not necessary. Must not mention anything about the prompt text.';
		return $final_prompt;
	}
	private function checkIfHistoryContainsImages($lastThreeMessages){
		foreach ($lastThreeMessages as $message) {
			if($message->images != null && $message->images != '' && $message->images != []){
				return true;
			}
		}
		return false;
	}

    public function index()
    {
        abort_if(Helper::setting('feature_ai_advanced_editor') == 0, 404); 
        $apiUrl = base64_encode('https://api.openai.com/v1/chat/completions');
		$settings_two = SettingTwo::first();
		if ($settings_two->openai_default_stream_server == 'backend') {
			$apikeyPart1 = base64_encode(rand(1, 100));
			$apikeyPart2 = base64_encode(rand(1, 100));
			$apikeyPart3 = base64_encode(rand(1, 100));
		}else{
			$settings = Setting::first();
			// Fetch the Site Settings object with openai_api_secret
			if ($settings?->user_api_option) {
				$apiKeys = explode(',', auth()->user()?->api_keys);
			} else {
				$apiKeys = explode(',', $settings?->openai_api_secret);
			}
			$apiKey = $apiKeys[array_rand($apiKeys)];
			$len = strlen($apiKey);
			$parts[] = substr($apiKey, 0, $l[] = rand(1, $len - 5));
			$parts[] = substr($apiKey, $l[0], $l[] = rand(1, $len - $l[0] - 3));
			$parts[] = substr($apiKey, array_sum($l));
			$apikeyPart1 = base64_encode($parts[0]);
			$apikeyPart2 = base64_encode($parts[1]);
			$apikeyPart3 = base64_encode($parts[2]);
		}

        return view('panel.user.generator.index', [
            'list' => OpenAIGenerator::query()
                ->where('active', true)->get(),
            'filters' => OpenaiGeneratorFilter::query()
                ->where(function ($query) {
                    $query->where('user_id', auth()->id())
                        ->orWhereNull('user_id');
                })
                ->get(),
            'apikeyPart1' => $apikeyPart1,
            'apikeyPart2' => $apikeyPart2,
            'apikeyPart3' => $apikeyPart3,
            'apiUrl' => $apiUrl,
        ]);
    }

    public function generator(Request $request, $slug)
    {

    }

    public function generatorOptions(Request $request, $slug)
    {
        $openai = OpenAIGenerator::query()
            ->where('slug', $slug)
            ->firstOrFail();
        $apiUrl = base64_encode('https://api.openai.com/v1/chat/completions');
		$settings_two = SettingTwo::first();
		if ($settings_two->openai_default_stream_server == 'backend') {
			$apikeyPart1 = base64_encode(rand(1, 100));
			$apikeyPart2 = base64_encode(rand(1, 100));
			$apikeyPart3 = base64_encode(rand(1, 100));
		}else{
			$settings = Setting::first();
			// Fetch the Site Settings object with openai_api_secret
			if ($settings?->user_api_option) {
				$apiKeys = explode(',', auth()->user()?->api_keys);
			} else {
				$apiKeys = explode(',', $settings?->openai_api_secret);
			}
			$apiKey = $apiKeys[array_rand($apiKeys)];

			$len = strlen($apiKey);
			$parts[] = substr($apiKey, 0, $l[] = rand(1, $len - 5));
			$parts[] = substr($apiKey, $l[0], $l[] = rand(1, $len - $l[0] - 3));
			$parts[] = substr($apiKey, array_sum($l));
			$apikeyPart1 = base64_encode($parts[0]);
			$apikeyPart2 = base64_encode($parts[1]);
			$apikeyPart3 = base64_encode($parts[2]);
		}

        $apiSearch = base64_encode('https://google.serper.dev/search');
        $auth = $request->user();
        return view(
            'panel.user.generator.components.generator-options',
            compact(
                'slug',
                'openai',
                'apiSearch',
                'apikeyPart1',
                'apikeyPart2',
                'apikeyPart3',
                'apiUrl',
                'auth'
            )
        )->render();
    }

    protected function openai(Request $request)
    {
        $team = $request->user()->getAttribute('team');

        $myCreatedTeam = $request->user()->getAttribute('myCreatedTeam');

        return UserOpenai::query()
            ->where(function (Builder $query) use ($team, $myCreatedTeam) {
                $query->where('user_id', auth()->id())
                    ->when($team || $myCreatedTeam, function ($query) use ($team, $myCreatedTeam) {
                        if ($team && $team?->is_shared) {
                            $query->orWhere('team_id', $team->id);
                        }
                        if ($myCreatedTeam) {
                            $query->orWhere('team_id', $myCreatedTeam->id);
                        }
                    });
            });
    }
}