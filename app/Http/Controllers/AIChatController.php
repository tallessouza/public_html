<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\Helper;
use App\Models\Chatbot\Chatbot;
use App\Models\ChatBotHistory;
use App\Models\ChatCategory;
use App\Models\Favourite;
use App\Models\OpenaiGeneratorChatCategory;
use App\Models\PdfData;
use App\Models\RateLimit;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\UserOpenaiChat;
use App\Models\UserOpenaiChatMessage;
use App\Services\Ai\Anthropic;
use App\Services\GatewaySelector;
use App\Services\VectorService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;
use ZipArchive;

class AIChatController extends Controller
{
    protected $client;

    protected $settings;
	protected $settings_two;

    public function __construct()
    {
        //Settings
        $this->settings = Setting::first();
		$this->settings_two = SettingTwo::first();
        // Fetch the Site Settings object with openai_api_secret
        if ($this->settings?->user_api_option) {
            $apiKeys = explode(',', auth()->user()?->api_keys);
        } else {
            $apiKeys = explode(',', $this->settings?->openai_api_secret);
        }
        $apiKey = $apiKeys[array_rand($apiKeys)];
        config(['openai.api_key' => $apiKey]);
    }

    public function openAIChatList()
    {
        abort_if(Helper::setting('feature_ai_chat') == 0, 404);

   	  $aiList = OpenaiGeneratorChatCategory::query()
            ->whereNotIn('slug', [
                'ai_vision', 'ai_webchat', 'ai_pdf'
            ])
            ->when(Auth::user()->isUser(), function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('user_id')->orWhere('user_id', Auth::id());
                });
            })
            ->get();

        $categoryList = ChatCategory::where('user_id', 1)->orWhere('user_id', auth()->user()->id)->get();
        $favData = Favourite::where('type', 'chat')
            ->where('user_id', auth()->user()->id)
            ->get();
        $message = false;

        return view('panel.user.openai_chat.list', compact('aiList', 'categoryList', 'favData', 'message'));
    }

    public function search(Request $request)
    {

        $categoryId = $request->category_id;
        $search = $request->search_word;

        $list = UserOpenaiChat::where('user_id', Auth::id())->where('openai_chat_category_id', $categoryId)->where('is_chatbot', 0)->orderBy('updated_at', 'desc')->where('title', 'like', "%$search%");

        $list = $list->get();
        $html = view('panel.user.openai_chat.components.chat_sidebar_list', compact('list'))->render();

        return response()->json(compact('html'));
    }

    public function openAIChat($slug)
    {
        $isPaid = false;
        $userId = Auth::user()->id;
        // Get current active subscription
        $activeSub = getCurrentActiveSubscription();
        if ($activeSub != null) {
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa();
            if ($activeSubY != null) {
                $gateway = $activeSubY->paid_with;
            }
        }

        try {
            $isPaid = GatewaySelector::selectGateway($gateway)::getSubscriptionStatus();
        } catch (\Exception $e) {
            $isPaid = false;
        }

        $category = OpenaiGeneratorChatCategory::whereSlug($slug)->firstOrFail();

        if ($isPaid == false && $category->plan == 'premium' && auth()->user()->type !== 'admin') {
            //$aiList = OpenaiGeneratorChatCategory::all();
            $aiList = OpenaiGeneratorChatCategory::where('slug', '<>', 'ai_vision')->where('slug', '<>', 'ai_pdf')->get();
            $categoryList = ChatCategory::all();
            $favData = Favourite::where('type', 'chat')
                ->where('user_id', auth()->user()->id)
                ->get();
            $message = true;

            return redirect()->route('dashboard.user.openai.chat.list')->with(compact('aiList', 'categoryList', 'favData', 'message'));
        }

        $list = $this->openai(\request())
            ->where('openai_chat_category_id', $category->id)
            ->where('is_chatbot', 0)
            ->orderBy('updated_at', 'desc');
        $list = $list->get();
        $chat = $list->first();
        //$aiList = OpenaiGeneratorChatCategory::all();
        $aiList = OpenaiGeneratorChatCategory::where('slug', '<>', 'ai_vision')->where('slug', '<>', 'ai_pdf')->get();

        //FOR LOW
        $settings = Setting::first();
        $settings2 = SettingTwo::first();
        $apiUrl = base64_encode('https://api.openai.com/v1/chat/completions');
		if ($settings2->openai_default_stream_server == 'backend') {
			$apikeyPart1 = base64_encode(rand(1, 100));
			$apikeyPart2 = base64_encode(rand(1, 100));
			$apikeyPart3 = base64_encode(rand(1, 100));
		}else{
			// Fetch the Site Settings object with openai_api_secret
			if ($this->settings?->user_api_option) {
				$apiKeys = explode(',', auth()->user()?->api_keys);
			} else {
				$apiKeys = explode(',', $this->settings?->openai_api_secret);
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
        $apiSearchId = base64_encode($settings2->serper_api_key);

        $lastThreeMessage = null;
        $chat_completions = null;

        if ($chat != null) {
            $lastThreeMessageQuery = $chat->messages()->whereNot('input', null)->orderBy('created_at', 'desc')->take(2);
            $lastThreeMessage = $lastThreeMessageQuery->get()->reverse();
            $category = OpenaiGeneratorChatCategory::where('id', $chat->openai_chat_category_id)->first();
            $chat_completions = str_replace(["\r", "\n"], '', $category->chat_completions) ?? null;

            if ($chat_completions != null) {
                $chat_completions = json_decode($chat_completions, true);
            }
        }

        $chatbots = Chatbot::query()->get();

        //FOR LOW END

        return view('panel.user.openai_chat.chat', compact(
            'category',
            'apiSearch',
            'chatbots',
            'apiSearchId',
            'list',
            'chat',
            'aiList',
            'apikeyPart1',
            'apikeyPart2',
            'apikeyPart3',
            'apiUrl',
            'lastThreeMessage',
            'chat_completions',
        ));
    }

    protected function openai(Request $request)
    {
        $team = $request->user()->getAttribute('team');

        $myCreatedTeam = $request->user()->getAttribute('myCreatedTeam');

        return UserOpenaiChat::query()
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

    public function openChatAreaContainer(Request $request)
    {
        $chat = UserOpenaiChat::where('id', $request->chat_id)->first();
        $category = $chat->category;
        $html = view('panel.user.openai_chat.components.chat_area_container', compact('chat', 'category'))->render();
        $lastThreeMessageQuery = $chat->messages()->whereNot('input', null)->orderBy('created_at', 'desc')->take(2);
        $lastThreeMessage = $lastThreeMessageQuery->get()->toArray();

        return response()->json(compact('html', 'lastThreeMessage'));
    }

    public function openChatBotArea(Request $request)
    {
        $chat = UserOpenaiChat::query()
            ->where('id', $request->chat_id)
            ->first();

        $category = $chat->category;

        $html = view('panel.user.openai_chat.components.chat_area', compact('chat', 'category'))->render();
        $lastThreeMessageQuery = $chat->messages()->whereNot('input', null)->orderBy('created_at', 'desc');
        $lastThreeMessage = $lastThreeMessageQuery->get()->toArray();

        return response()->json(compact('html', 'lastThreeMessage'));
    }

    public function startNewChat(Request $request)
    {
        $category = OpenaiGeneratorChatCategory::where('id', $request->category_id)->firstOrFail();

        $chatbot = Chatbot::query()->where('id', $category->chatbot_id)->first();

        $chat = new UserOpenaiChat();
        $chat->user_id = Auth::id();
        $chat->team_id = Auth::user()->team_id;
        $chat->chatbot_id = $category->chatbot_id;
        $chat->openai_chat_category_id = $category->id;
        $chat->title = $category->name.' Chat';
        $chat->total_credits = 0;
        $chat->total_words = 0;
        $chat->save();

        $message = new UserOpenaiChatMessage();
        $message->user_openai_chat_id = $chat->id;
        $message->user_id = Auth::id();
        $message->response = 'First Initiation';
        if ($category->slug != 'ai_vision' || $category->slug != 'ai_pdf') {
            if ($category->role == 'default') {
                $output = __('Hi! I am').' '.$category->name.__(', and I\'m here to answer all your questions');
            } else {
                $output = __('Hi! I am').' '.$category->human_name.__(', and I\'m').' '.$category->role.'. '.$category->helps_with;
            }
        } else {
            $output = null;
        }

        if ($chatbot) {
            if ($chatbot->first_message != null) {
                $output = $chatbot->first_message;
            }
        }

        if ($category) {
            if ($category->first_message != null) {
                $output = $category->first_message;
            }
        }

        $message->output = $output;
        $message->hash = Str::random(256);
        $message->credits = 0;
        $message->words = 0;
        $message->save();

        $list = UserOpenaiChat::where('user_id', Auth::id())->where('openai_chat_category_id', $category->id)->where('is_chatbot', 0)->orderBy('updated_at', 'desc')->get();
        $html = view('panel.user.openai_chat.components.chat_area_container', compact('chat', 'category'))->render();
        $html2 = view('panel.user.openai_chat.components.chat_sidebar_list', compact('list', 'chat'))->render();

        return response()->json(compact('html', 'html2', 'chat'));
    }

    public function doc_to_text($path_to_file)
    {
        $fileHandle = fopen($path_to_file, 'r');
        $line = @fread($fileHandle, filesize($path_to_file));
        $lines = explode(chr(0x0D), $line);
        $response = '';

        foreach ($lines as $current_line) {

            $pos = strpos($current_line, chr(0x00));

            if (($pos !== false) || (strlen($current_line) == 0)) {
            } else {
                $response .= $current_line.' ';
            }
        }

        $response = preg_replace('/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/', '', $response);

        return $response;
    }

    public function docx_to_text($path_to_file)
    {
        $response = '';
        $zip = new ZipArchive();

        if (! $zip->open($path_to_file)) {
            return false;
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->statIndex($i);

            if ($entry['name'] !== 'word/document.xml') {
                continue;
            }

            $content = $zip->getFromIndex($i);

            // Remove unnecessary XML tags
            $content = str_replace(['</w:r></w:p></w:tc><w:tc>', '</w:r></w:p>'], ["\r\n", "\n"], $content);
            $content = strip_tags($content);

            $response .= $content;
        }

        $zip->close();

        return $response;
    }

    public function uploadDoc(Request $request, $chat_id, $type)
    {
        Helper::setOpenAiKey();

        if ($type == 'application/pdf') {
            $type = 'pdf';
        } elseif ($type == 'application/msword') {
            $type = 'doc';
        } elseif ($type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            $type = 'docx';
        } elseif ($type == 'text/csv') {
            $type = 'csv';
        }
        $doc = $request->file('doc');
        $doc_content = file_get_contents($doc->getRealPath());
        $fileName = Str::random(12).'.'.$type;
        Storage::disk('public')->put('temp.'.$type, $doc_content);
        Storage::disk('public')->put($fileName, $doc_content);

        $uploadedFile = new File(substr("/uploads/$fileName", 1));

        $resPath = "/uploads/$fileName";

        if (SettingTwo::first()->ai_image_storage == 's3') {
            try {
                $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                unlink(substr("/uploads/$fileName", 1));
                $resPath = Storage::disk('s3')->url($aws_path);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'AWS Error - '.$e->getMessage()]);
            }
        }

        if ($type == 'pdf') {
            $parser = new \Smalot\PdfParser\Parser();
            $text = $parser->parseFile('uploads/temp.pdf')->getText();

            $page = $text;
            if (! mb_check_encoding($text, 'UTF-8')) {
                $page = mb_convert_encoding($text, 'UTF-8', mb_detect_encoding($text));
            } else {
                $page = $text;
            }
        } elseif ($type == 'docx') {
            $filePath = public_path('uploads/temp.'.$type);
            $page = $this->docx_to_text($filePath);
            Log::info($page);
        } elseif ($type == 'doc') {
            $filePath = public_path('uploads/temp.'.$type);
            $page = $this->doc_to_text($filePath);
            Log::info($page);
        } elseif ($type == 'csv') {
            $file = file_get_contents(public_path('uploads/temp.'.$type));
            $rows = explode(PHP_EOL, $file);

            $header = str_getcsv(array_shift($rows)); // Get header row
            Log::info(json_encode($header));
            $dataAsJson = [];
            foreach ($rows as $row) {
                Log::info(count($header));
                Log::info(json_encode(array_pad(str_getcsv($row), count($header), '')));
                $data = array_combine($header, array_pad(str_getcsv($row), count($header), '')); // Combine header with data
                $dataAsJson[] = json_encode($data);
                Log::info($row);
            }
            $page = implode("\n", $dataAsJson);
            Log::info($page);
        }

        $countwords = strlen($page) / 1001 + 1;
        for ($i = 0; $i < $countwords; $i++) {
            if (1001 * $i + 2000 > strlen($page)) {
                try {
                    $subtxt = substr($page, 1001 * $i, strlen($page) - 1001 * $i);
                    $subtxt = mb_convert_encoding($subtxt, 'UTF-8', 'UTF-8');
                    $subtxt = iconv('UTF-8', 'UTF-8//IGNORE', $subtxt);
                    $response = OpenAI::embeddings()->create([
                        'model' => 'text-embedding-ada-002',
                        'input' => $subtxt,
                    ]);

                    if (strlen(substr($page, 1001 * $i, strlen($page) - 1001 * $i)) > 10) {

                        $chatpdf = new PdfData();

                        $chatpdf->chat_id = $chat_id;
                        $chatpdf->content = substr($page, 1001 * $i, strlen($page) - 1001 * $i);
                        $chatpdf->vector = json_encode($response->embeddings[0]->embedding);

                        $chatpdf->save();
                    }
                } catch (Exception $e) {
                }
            } else {
                try {
                    $subtxt = substr($page, 1001 * $i, 2000);
                    $subtxt = mb_convert_encoding($subtxt, 'UTF-8', 'UTF-8');
                    $subtxt = iconv('UTF-8', 'UTF-8//IGNORE', $subtxt);
                    $response = OpenAI::embeddings()->create([
                        'model' => 'text-embedding-ada-002',
                        'input' => $subtxt,
                    ]);
                    if (strlen(substr($page, 1001 * $i, 2000)) > 10) {
                        $chatpdf = new PdfData();

                        $chatpdf->chat_id = $chat_id;
                        $chatpdf->content = substr($page, 1001 * $i, 2000);
                        $chatpdf->vector = json_encode($response->embeddings[0]->embedding);

                        $chatpdf->save();
                    }
                } catch (Exception $e) {
                }
            }
        }

        return $resPath;
    }

    public function startNewDocChat(Request $request)
    {
        $category = OpenaiGeneratorChatCategory::where('id', $request->category_id)->firstOrFail();
        $chat = new UserOpenaiChat();
        $chat->user_id = Auth::id();
        $chat->team_id = Auth::user()->team_id;
        $chat->openai_chat_category_id = $category->id;
        $chat->title = $category->name.' Chat';
        $chat->total_credits = 0;
        $chat->total_words = 0;
        $chat->save();

        try {
            $filePath = $this->uploadDoc($request, $chat->id, $request->type);
            $chat->reference_url = $filePath;
            $chat->doc_name = $request->file('doc')->getClientOriginalName();
            $chat->save();

            $message = new UserOpenaiChatMessage();
            $message->user_openai_chat_id = $chat->id;
            $message->user_id = Auth::id();
            $message->response = 'First Initiation';
            if ($category->slug != 'ai_vision' || $category->slug != 'ai_pdf') {
                if ($category->role == 'default') {
                    $output = __('Hi! I am').' '.$category->name.__(', and I\'m here to answer all your questions');
                } else {
                    $output = __('Hi! I am').' '.$category->human_name.__(', and I\'m').' '.$category->role.'. '.$category->helps_with;
                }
            } else {
                $output = null;
            }
            $message->output = $output;
            $message->hash = Str::random(256);
            $message->credits = 0;
            $message->words = 0;
            $message->save();

            $list = UserOpenaiChat::where('user_id', Auth::id())->where('openai_chat_category_id', $category->id)->where('is_chatbot', 0)->orderBy('updated_at', 'desc')->get();
            $html = view('panel.user.openai_chat.components.chat_area_container', compact('chat', 'category'))->render();
            $html2 = view('panel.user.openai_chat.components.chat_sidebar_list', compact('list', 'chat'))->render();

            return response()->json(compact('html', 'html2', 'chat'));
        } catch (Exception $e) {
            $chat->delete();

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function startNewChatBot(Request $request)
    {
        $settings_two = SettingTwo::first();

        $chatbot = Chatbot::query()->where('id', $settings_two->chatbot_template)->firstOrFail();

        $category = $chatbot;

        $chat = new UserOpenaiChat();
        $chat->user_id = Auth::id();
        $chat->chatbot_id = $chatbot->id;
        //        $chat->openai_chat_category_id = $category->id;
        $chat->title = 'ChatBot';
        $chat->total_credits = 0;
        $chat->total_words = 0;
        $chat->is_chatbot = 1;
        $chat->save();

        $message = new UserOpenaiChatMessage();
        $message->user_openai_chat_id = $chat->id;
        $message->user_id = Auth::id();
        $message->response = 'First Initiation';
        $output = $category->first_message ?: 'How can I help you?';
        $message->output = $output;
        $message->hash = Str::random(256);
        $message->credits = 0;
        $message->words = 0;
        $message->is_chatbot = 1;
        $message->save();

        $chatbot_history = new ChatBotHistory();
        $chatbot_history->user_id = Auth::id();
        $chatbot_history->ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : request()->ip();
        $chatbot_history->user_openai_chat_id = $chat->id;
        $chatbot_history->openai_chat_category_id = $category->id;
        $chatbot_history->save();

        $html = view('panel.user.openai_chat.components.chat_area', compact('chat', 'category'))->render();

        return response()->json(compact('html', 'chat'));
    }

    public function chatOutput(Request $request)
    {
		$user = Auth::user();
		$chat_id = $request->get('chat_id');
		$chat = UserOpenaiChat::whereId($chat_id)->first();
		
		if ($user->remaining_words <= 0) {
			if ($user->remaining_words != -1) {
				$data = [
					'errors' => [__('You have no credits left. Please consider upgrading your plan.')],
				];
				return response()->json($data, 419);
			}
		}

		if ($request->isMethod('post')) {
			$prompt = $request->get('prompt');
			// if ($chat->category->prompt_prefix != null && !str_starts_with($chat->category->slug, 'ai_')) {
			//     $prompt = "You will now play a character and respond as that character (You will never break character). Your name is". $chat->category->human_name. ". I want you to act as a". $chat->category->role . ". ". $chat->category->prompt_prefix . ' ' . $prompt;
			// } 
			$realtime = $request->get('realtime');
			$total_used_tokens = 0;
            $entry = new UserOpenaiChatMessage();
            $entry->user_id = $user->id;
            $entry->user_openai_chat_id = $chat->id;
            $entry->input = $prompt;
            $entry->response = null;
            $entry->realtime = $realtime ?? 0;
            $entry->output = "(If you encounter this message, please attempt to send your message again. If the error persists beyond multiple attempts, please don't hesitate to contact us for assistance!)";
            $entry->hash = Str::random(256);
            $entry->credits = $total_used_tokens;
            $entry->words = 0;
            $entry->save();
            $user->save();
            $chat->total_credits += $total_used_tokens;
            $chat->save();
            $message_id = $entry->id;

            return response()->json(compact('message_id'));
		}
		elseif ($request->isMethod('get')) {
			$type = $request->get('type');
			if ($chat->category->slug == 'ai_pdf') {
				return self::pdfStream($request);
			}
			else if ($chat->category->slug == 'ai_webchat') {
				return self::webChatStream($request);
			}
			else{
				switch ($type) {
					case 'chat':
						return self::chatbotsStream($request);
						break;
					case 'vision':
						return self::visionStream($request);
						break;
					default:
						return self::chatbotsStream($request);
						break;
				}
			}
		}
		else{
			return response()->json(['message' => 'Method not allowed'], 405);
		}
    }
	private function pdfStream(Request $request)
	{
        $openaiApiKey = Helper::setOpenAiKey();

        if (setting('default_ai_engine', 'openai') == 'anthropic') {
            $openaiApiKey = Helper::setAnthropicKey();
        }

		$chat_id = $request->get('chat_id');
		$message_id = $request->get('message_id');
		// $prompt = $request->get('prompt');
		$message = UserOpenaiChatMessage::whereId($message_id)->first();
		$prompt = $message->input;

		$chat_bot = $this->settings?->openai_default_model;
		$chat_bot == null ? 'gpt-3.5-turbo': $chat_bot;       
		$userId = Auth::user()->id;
		$history = [];


        if (setting('default_ai_engine', 'openai') == 'anthropic') {
            $chat_bot = setting('anthropic_default_model', 'claude-3-opus-20240229');
        }
		
		$chat = UserOpenaiChat::whereId($chat_id)->first();
		# check if there completions for the template
		$category = $chat->category;
		if ($category->chat_completions) {
			$chat_completions = json_decode($category->chat_completions, true);
			foreach ($chat_completions as $item) {
				$history[] = [
					'role' => $item['role'],
					'content' => $item['content'] ?? '',
				];
			}
		} else {
			$history[] = ['role' => 'system', 'content' => 'You are a helpful assistant.'];
		}

		# follow the context of the last 5 messages
		$lastThreeMessageQuery = $chat->messages()
		->whereNotNull('input')
		->orderBy('created_at', 'desc')
		->take(4)
		->get()
		->reverse();

		$vectorService = new VectorService();

		$extra_prompt = $vectorService->getMostSimilarText($prompt, $chat_id, 5 , $chat->chatbot_id);
		$count = count($lastThreeMessageQuery);
		if ($count > 1) {
			$lastThreeMessageQuery[$count - 1]->input = "'this file' means file content. Must not reference previous chats if user asking about pdf. Must reference file content if only user is asking about file content. Else just response as an assistant shortly and professionaly without must not referencing file content. \n\n\n\n\nUser qusetion: $prompt \n\n\n\n\n Document Content: \n $extra_prompt";
			foreach ($lastThreeMessageQuery as $threeMessage) {
				$history[] = ['role' => 'user', 'content' => $threeMessage->input ?? ''];
				if ($threeMessage->output != null) {
					$history[] = ['role' => 'assistant', 'content' => $threeMessage->output ?? ''];
				}else{
					$history[] = ['role' => 'assistant', 'content' => ''];
				}
			}
		}else{
			$history[] = ['role' => 'user', 'content' => "'this file' means file content. Must not reference previous chats if user asking about pdf. Must reference file content if only user is asking about file content. Else just response as an assistant shortly and professionaly without must not referencing file content. . User: $prompt \n\n\n\n\n Document Content: \n $extra_prompt"];
		}

		return self::openaiChatStream($request, $openaiApiKey, $chat_bot, $history,$message_id);
	}
	private function chatbotsStream(Request $request)
	{
        $openaiApiKey = Helper::setOpenAiKey();

        if (setting('default_ai_engine', 'openai') == 'anthropic') {
            $openaiApiKey = Helper::setAnthropicKey();
        }

		$chat_id = $request->get('chat_id');
		
		$message_id = $request->get('message_id');
		// $prompt = $request->get('prompt');
		$message = UserOpenaiChatMessage::whereId($message_id)->first();
		$prompt = $message->input;

		$realtime = $request->get('realtime');
		$chat_bot = $this->settings?->openai_default_model;
		$chat_bot == null ? 'gpt-3.5-turbo': $chat_bot;       
		$userId = Auth::user()->id;
		$history = [];
		$realtimePrompt = $prompt;
		
		$chat = UserOpenaiChat::whereId($chat_id)->first();
		# check if there completions for the template
		$category = $chat->category;
		if ($category->chat_completions) {
			$chat_completions = json_decode($category->chat_completions, true);
			foreach ($chat_completions as $item) {
				$history[] = [
					'role' => $item['role'],
					'content' => $item['content']?? '',
				];
			}
		} else {
			$history[] = ['role' => 'system', 'content' => 'You are a helpful assistant.'];
		}


        if ($category && $category?->instructions) {
            $history[] = ['role' => 'system', 'content' => $category->instructions ];
        }

        $extra_prompt = null;

        if ($category->chatbot_id) {
            try {
                $extra_prompt = (new VectorService())->getMostSimilarText($prompt, $chat_id, 2, $category->chatbot_id);
                if ($extra_prompt) {
                    $history[] = ['role' => 'user', 'content' => "'this file' means file content. Must not reference previous chats if user asking about pdf. Must reference file content if only user is asking about file content. Else just response as an assistant shortly and professionaly without must not referencing file content. . User: $prompt \n\n\n\n\n Document Content: \n $extra_prompt"];
                }
            } catch (\Throwable $th) {
            }
        }

        # follow the context of the last 5 messages
		$lastThreeMessageQuery = $chat->messages()
		->whereNotNull('input')
		->orderBy('created_at', 'desc')
		->take(4)
		->get()
		->reverse();

		$count = count($lastThreeMessageQuery);
		if ($count > 1) {
			foreach ($lastThreeMessageQuery as $threeMessage) {
				$history[] = ['role' => 'user', 'content' => $threeMessage->input?? ''];
				if ($threeMessage->output != null) {
					$history[] = ['role' => 'assistant', 'content' => $threeMessage->output?? ''];
				}else{
					$history[] = ['role' => 'assistant', 'content' => ''];
				}
			}
			if($realtime && $this->settings_two->serper_api_key != null){
				$sclient = new Client();
				$headers = [
					'X-API-KEY' => $this->settings_two->serper_api_key,
					'Content-Type' => 'application/json',
				];
				$body = [
					'q' => $realtimePrompt,
				];
				$response = $sclient->post('https://google.serper.dev/search', [
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
				$history[] = ['role' => 'user', 'content' => $final_prompt?? ''];
			}else{
				$history[] = ['role' => 'user', 'content' => $prompt?? ''];
			}
		}else{
			if($realtime && $this->settings_two->serper_api_key != null){
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
				$history[] = ['role' => 'user', 'content' => $final_prompt?? ''];
			}else{
				$history[] = ['role' => 'user', 'content' => $prompt?? ''];
			}
		}

		return self::openaiChatStream($request, $openaiApiKey, $chat_bot, $history,$message_id, null, [], $category);
	}
	private function webChatStream(Request $request)
	{
        $openaiApiKey = Helper::setOpenAiKey();

        if (setting('default_ai_engine', 'openai') == 'anthropic') {
            $openaiApiKey = Helper::setAnthropicKey();
        }

		$chat_id = $request->get('chat_id');
		
		$message_id = $request->get('message_id');
		// $prompt = $request->get('prompt');
		$message = UserOpenaiChatMessage::whereId($message_id)->first();
		$prompt = $message->input;

		$realtime = $request->get('realtime');
		$chat_bot = $this->settings?->openai_default_model;
		$chat_bot == null ? 'gpt-3.5-turbo': $chat_bot;       
		$userId = Auth::user()->id;
		$history = [];
		$realtimePrompt = $prompt;
		
		$chat = UserOpenaiChat::whereId($chat_id)->first();
		# check if there completions for the template
		$category = $chat->category;
		if ($category->chat_completions) {
			$chat_completions = json_decode($category->chat_completions, true);
			foreach ($chat_completions as $item) {
				$history[] = [
					'role' => $item['role'],
					'content' => $item['content']?? '',
				];
			}
		} else {
			$history[] = ['role' => 'system', 'content' => 'You are a helpful assistant.'];
		}


        if ($category && $category?->instructions) {
            $history[] = ['role' => 'system', 'content' => $category->instructions ];
        }

        $extra_prompt = null;

        try {
            $extra_prompt = (new VectorService())->getMostSimilarText($prompt, $chat_id, 2);
            if ($extra_prompt) {
                $history[] = ['role' => 'user', 'content' => "'this file' means file content. Must not reference previous chats if user asking about pdf. Must reference file content if only user is asking about file content. Else just response as an assistant shortly and professionaly without must not referencing file content. . User: $prompt \n\n\n\n\n Document Content: \n $extra_prompt"];
            }
        } catch (\Throwable $th) {

        }

        # follow the context of the last 5 messages
		$lastThreeMessageQuery = $chat->messages()
		->whereNotNull('input')
		->orderBy('created_at', 'desc')
		->take(4)
		->get()
		->reverse();

		$count = count($lastThreeMessageQuery);
		if ($count > 1) {
			foreach ($lastThreeMessageQuery as $threeMessage) {
				$history[] = ['role' => 'user', 'content' => $threeMessage->input?? ''];
				if ($threeMessage->output != null) {
					$history[] = ['role' => 'assistant', 'content' => $threeMessage->output?? ''];
				}else{
					$history[] = ['role' => 'assistant', 'content' => ''];
				}
			}
			if($realtime && $this->settings_two->serper_api_key != null){
				$sclient = new Client();
				$headers = [
					'X-API-KEY' => $this->settings_two->serper_api_key,
					'Content-Type' => 'application/json',
				];
				$body = [
					'q' => $realtimePrompt,
				];
				$response = $sclient->post('https://google.serper.dev/search', [
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
				$history[] = ['role' => 'user', 'content' => $final_prompt?? ''];
			}else{
				$history[] = ['role' => 'user', 'content' => $prompt?? ''];
			}
		}else{
			if($realtime && $this->settings_two->serper_api_key != null){
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
				$history[] = ['role' => 'user', 'content' => $final_prompt?? ''];
			}else{
				$history[] = ['role' => 'user', 'content' => $prompt?? ''];
			}
		}


		return self::openaiChatStream($request, $openaiApiKey, $chat_bot, $history, $message_id, null, [], $category);
	}
	private function visionStream(Request $request)
	{
		$openaiKey = $this->settings->user_api_option ? explode(',', auth()->user()->api_keys) : explode(',', $this->settings->openai_api_secret);
		$openaiApiKey = $openaiKey[array_rand($openaiKey)];
		$chat_id = $request->get('chat_id');
		
		$message_id = $request->get('message_id');
		// $prompt = $request->get('prompt');
		$message = UserOpenaiChatMessage::whereId($message_id)->first();
		$prompt = $message->input;
		$realtime = $request->get('realtime');
		$chat_bot = 'gpt-4-vision-preview';
		$userId = Auth::user()->id;
		$history = [];
		$realtimePrompt = $prompt;
		
		$chat = UserOpenaiChat::whereId($chat_id)->first();
		# add vision completions for the template
		$history[] = [
			'role' => 'system',
			'content' => "You will now play a character and respond as that character (You will never break character). Your name is Vision AI. Must not introduce by yourself as well as greetings. Help also with asked questions based on previous responses and images if exists."
		];
		# follow the context of the last 5 messages
		$lastThreeMessageQuery = $chat->messages()
		->whereNotNull('input')
		->orderBy('created_at', 'desc')
		->take(4)
		->get()
		->reverse();
		// $images = json_decode($request->get('images'), true);
		$images = explode(',', $request->images);
		$count = count($lastThreeMessageQuery);
		if ($count > 1) {
			foreach ($lastThreeMessageQuery as $threeMessage) {
				$history[] = [
					'role' => 'user', 
					'content' => array_merge(
						[
							[
								'type' => 'text',
								'text' => $threeMessage->input,
							],
						],
						collect($threeMessage->images)->map(function ($item) {
							if($item !== "undefined" || $item !== null) {
								if (Str::startsWith($item, 'http')) {
									$imageData = file_get_contents($item);
								} else {
									$imageData = file_get_contents(substr($item, 1, strlen($item) - 1));
								}
								$base64Image = base64_encode($imageData);

								return [
									'type' => 'image_url',
									'image_url' => [
										'url' => 'data:image/png;base64,'.$base64Image,
									],
								];
							}
						})->toArray()
					),
				];
				if ($threeMessage->response != null) {
					$history[] = ['role' => 'assistant', 'content' => $threeMessage->response];
				}
			}
		}
		$history[] = 
		[	
			'role' => 'user',
			'content' => array_merge(
				[
					[
						'type' => 'text',
						'text' => $prompt,
					],
				],
				collect($images)->map(function ($item) {
					if (Str::startsWith($item, 'http')) {
						$imageData = file_get_contents($item);
					} else {
						$imageData = file_get_contents(substr($item, 1, strlen($item) - 1));
					}
					$base64Image = base64_encode($imageData);

					return [
						'type' => 'image_url',
						'image_url' => [
							'url' => 'data:image/png;base64,'.$base64Image,
						],
					];
				})->toArray()
			),
		];

		return self::openaiChatStream($request, $openaiApiKey, $chat_bot, $history,$message_id, 2000, $images);
	}
	private function openaiChatStream($request, $openaiApiKey, $chat_bot, $history,  $message_id , $ai_max_tokens = null, $images = [], $category = null)
    {
        return response()->stream(function () use ($request, $openaiApiKey,$ai_max_tokens, $history, $chat_bot, $message_id ,$images, $category) {
			if ($ai_max_tokens !== null) {

                $openaiUse = setting('default_ai_engine', 'openai') == 'openai';

                if ($category) {
                    if ($category?->chatbot_id) {
                        $openaiUse = true;
                    }
                }

                if ($openaiUse) {
                    $gclient = new Client();
                    $url = 'https://api.openai.com/v1/chat/completions';
                    $headers = [
                        'Authorization' => 'Bearer '.$openaiApiKey,
                    ];

                    $postData = [
                        'headers' => $headers,
                        'json' => [
                            'model' => $chat_bot,
                            'messages' => $history,
                            'max_tokens' => $ai_max_tokens,
                            'stream' => true,
                        ],
                    ];

                    $response = $gclient->post($url, $postData);
                    $total_used_tokens = 0;
                    $output = '';
                    $responsedText = '';

                    foreach (explode("\n", $response->getBody()->getContents()) as $chunk) {
                        if (strlen($chunk) > 5 && $chunk != 'data: [DONE]' && isset(json_decode(substr($chunk, 6, strlen($chunk) - 6))->choices[0]->delta->content)) {

                            $message = json_decode(substr($chunk, 6, strlen($chunk) - 6))->choices[0]->delta->content;

                            $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);
                            $output .= $messageFix;

                            $responsedText .= $message;
                            $total_used_tokens += countWords($message);

                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);

                            echo PHP_EOL;
                            echo 'data: '.$messageFix.'/**'.$random_text."\n\n";
                            flush();
                            usleep(1000);
                        }
                    }
                }else {
                    $client = app(Anthropic::class);

                    $historyMessages = array_filter($history, function ($item) {
                        return $item['role'] != 'system';
                    });

                    $system = Arr::first(array_filter($history, function ($item) {
                        return $item['role'] == 'system';
                    }));

                    $system = data_get($system, 'content');

                    $data = $client->setStream(true)
                        ->setSystem($system)
                        ->setMessages(array_values($historyMessages))
                        ->stream()
                        ->body();

                    $total_used_tokens = 0;
                    $output = '';
                    $responsedText = '';

                    foreach (explode("\n", $data) as $chunk) {

                        if (strlen($chunk) < 6) {
                            continue;
                        }

                        if (! Str::contains($chunk, 'data: ')) {

                            continue;
                        }

                        $chunk = str_replace('data: {', '{', $chunk);

                        if (isset(json_decode($chunk)->delta->text)) {
                            $message = json_decode($chunk)->delta->text;

                            $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);

                            $output .= $messageFix;
                            $responsedText .= $message;

                            $total_used_tokens += countWords($message);
                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);

                            echo PHP_EOL;
                            echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                            flush();
                            //ob_flush();
                            usleep(1000);
                        }

                        if (connection_aborted()) {
                            break;
                        }
                    }
                }

			}else{

                $openaiUse = setting('default_ai_engine', 'openai') == 'openai';

                if ($category) {
                    if ($category?->chatbot_id) {
                        $openaiUse = true;
                    }
                }

                if ($openaiUse) {
                    $stream = OpenAI::chat()->createStreamed([
                        'model' => $chat_bot,
                        'messages' => $history,
                        'presence_penalty' => 0.6,
                        'frequency_penalty' => 0,
                    ]);
                    $total_used_tokens = 0;
                    $output = '';
                    $responsedText = '';
                    foreach ($stream as $response) {
                        if (isset($response['choices'][0]['delta']['content'])) {
                            $message = $response['choices'][0]['delta']['content'];
                            $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);

                            $output .= $messageFix;
                            $responsedText .= $message;

                            $total_used_tokens += countWords($message);
                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);

                            echo PHP_EOL;
                            echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                            flush();
                            //ob_flush();
                            usleep(1000);
                        }
                        if (connection_aborted()) {
                            break;
                        }
                    }
                } else {

                    $client = app(Anthropic::class);

                    $historyMessages = array_filter($history, function ($item) {
                        return $item['role'] != 'system';
                    });

                    $system = Arr::first(array_filter($history, function ($item) {
                        return $item['role'] == 'system';
                    }));

                    $system = data_get($system, 'content');

                    $data = $client->setStream(true)
                        ->setSystem($system)
                        ->setMessages(array_values($historyMessages))
                        ->stream()
                        ->body();

                    $total_used_tokens = 0;
                    $output = '';
                    $responsedText = '';

                    foreach (explode("\n", $data) as $chunk) {

                        if (strlen($chunk) < 6) {
                            continue;
                        }

                        if (! Str::contains($chunk, 'data: ')) {

                            continue;
                        }

                        $chunk = str_replace('data: {', '{', $chunk);

                        if (isset(json_decode($chunk)->delta->text)) {
                            $message = json_decode($chunk)->delta->text;

                            $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);

                            $output .= $messageFix;
                            $responsedText .= $message;

                            $total_used_tokens += countWords($message);
                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);

                            echo PHP_EOL;
                            echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                            flush();
                            //ob_flush();
                            usleep(1000);
                        }

                        if (connection_aborted()) {
                            break;
                        }
                    }
                }
			}
			
			$message = UserOpenaiChatMessage::whereId($message_id)->first();
			$chat_id = $message->user_openai_chat_id;
			$chat = UserOpenaiChat::whereId($chat_id)->first();

			$message->response = $responsedText;
			$message->output = $output;
			$message->hash = Str::random(256);
			$message->credits = $total_used_tokens;
			$message->words = 0;
			$message->images = implode(',', $images);
			$message->pdfName = $request->pdfname;
			$message->pdfPath = $request->pdfpath;
			$message->save();

			$user = Auth::user();

			if ($user->remaining_words != -1) {
				$user->remaining_words -= $total_used_tokens;
			}

			if ($user->remaining_words < -1) {
				$user->remaining_words = 0;
			}
			$user->save();

			$chat->total_credits += $total_used_tokens;
			$chat->save();
			echo 'data: [DONE]';
			echo "\n\n";
			flush();
			//ob_flush();
			usleep(1000);
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);

	}

    public function chatbotOutput(Request $request)
    {
        $settings2 = SettingTwo::first();
        $chatbot = Chatbot::where('id', $settings2->chatbot_template)->firstOrFail();

        if ($request->isMethod('get')) {

            $type = $request->type;
            $images = explode(',', $request->images);
            $chat_bot = null;
            $user = Auth::user();
            $userId = $user->id;

            $model = $chatbot->model;

            $chat_id = $request->chat_id;
            $message_id = $request->message_id;

            $user_id = Auth::id();

            $message = UserOpenaiChatMessage::whereId($message_id)->first();
            $prompt = $message->input;
            $realtime = $message->realtime;
            $realtimePrompt = $prompt;
            $chat = UserOpenaiChat::whereId($chat_id)->first();

            $lastThreeMessageQuery = $chat->messages()
                ->whereNotNull('input')
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get()
                ->reverse();
            $i = 0;

            $history[] = ['role' => 'system', 'content' => $chatbot->instructions ?: 'You are a helpful assistant.'];

            $vectorService = new VectorService();

            $extra_prompt = $vectorService->getMostSimilarText(
                $prompt,
                $chat_id,
                5,
                SettingTwo::query()->first()?->getAttribute('chatbot_template')
            );

            if (count($lastThreeMessageQuery) > 1 && ! $realtime) {
                if ($extra_prompt != '') {
                    $lastThreeMessageQuery[count($lastThreeMessageQuery) - 1]->input = "'this file' means file content. Must not reference previous chats if user asking about pdf. Must reference file content if only user is asking about file content. Else just response as an assistant shortly and professionaly without must not referencing file content. \n\n\n\n\nUser qusetion: $prompt \n\n\n\n\n Document Content: \n $extra_prompt";
                }

                foreach ($lastThreeMessageQuery as $threeMessage) {
                    $history[] = ['role' => 'user', 'content' => $threeMessage->input];
                    if ($threeMessage->response != null) {
                        $history[] = ['role' => 'assistant', 'content' => $threeMessage->response];
                    }
                }
            } else {
                if ($extra_prompt == '') {
                    if ($realtime && $settings2->serper_api_key != null) {
                        $client = new Client();
                        $headers = [
                            'X-API-KEY' => $settings2->serper_api_key,
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
                        // unset($history);
                        $history[] = ['role' => 'user', 'content' => $final_prompt];
                        // \Illuminate\Support\Facades\Log::error(json_encode($toGPT));
                    } else {
                        $history[] = ['role' => 'user', 'content' => $prompt];
                    }
                } else {
                    $history[] = ['role' => 'user', 'content' => "'this file' means file content. Must not reference previous chats if user asking about pdf. Must reference file content if only user is asking about file content. Else just response as an assistant shortly and professionaly without must not referencing file content. . User: $prompt \n\n\n\n\n Document Content: \n $extra_prompt"];
                }
            }

            return response()->stream(function () use ($request, $chat_id, $message_id, $history, $model, $type, $images) {
                if ($type == 'chat') {
                    try {
                        $stream = OpenAI::chat()->createStreamed([
                            'model' => $model,
                            'messages' => $history,
                            'presence_penalty' => 0.6,
                            'frequency_penalty' => 0,
                        ]);
                        $total_used_tokens = 0;
                        $output = '';
                        $responsedText = '';
                        foreach ($stream ?? [] as $response) {

                            if (isset($response['choices'][0]['delta']['content'])) {

                                $message = $response['choices'][0]['delta']['content'];
                                $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);
                                $output .= $messageFix;
                                $responsedText .= $message;
                                $total_used_tokens += countWords($message);
                                $string_length = Str::length($messageFix);
                                $needChars = 6000 - $string_length;
                                $random_text = Str::random($needChars);

                                echo PHP_EOL;
                                echo 'data: '.$messageFix.'/**'.$random_text."\n\n";
                                flush();
                                usleep(5000);
                            }
                            if (connection_aborted()) {
                                break;
                            }
                        }
                    } catch (\Exception $exception) {
                        $messageError = 'Error from API call. Please try again. If error persists again please contact system administrator with this message '.$exception->getMessage();
                        echo "data: $messageError";
                        echo "\n\n";
                        flush();
                        echo 'data: [DONE]';
                        echo "\n\n";
                        flush();
                        usleep(50000);
                    }
                } elseif ($type == 'vision') {

                    try {
                        $gclient = new Client();

                        if ($this->settings?->user_api_option) {
                            $apiKeys = explode(',', auth()->user()?->api_keys);
                        } else {
                            $apiKeys = explode(',', $this->settings?->openai_api_secret);
                        }
                        $openaiApiKey = $apiKeys[array_rand($apiKeys)];
                        $url = 'https://api.openai.com/v1/chat/completions';

                        $response = $gclient->post(
                            $url,
                            [
                                'headers' => [
                                    'Authorization' => 'Bearer '.$openaiApiKey,
                                ],
                                'json' => [
                                    'model' => 'gpt-4-vision-preview',
                                    'messages' => [
                                        [
                                            'role' => 'user',
                                            'content' => array_merge(
                                                [
                                                    [
                                                        'type' => 'text',
                                                        'text' => $prompt,
                                                    ],
                                                ],
                                                collect($images)->map(function ($item) {
                                                    if (Str::startsWith($item, 'http')) {
                                                        $imageData = file_get_contents($item);
                                                    } else {
                                                        $imageData = file_get_contents(substr($item, 1, strlen($item) - 1));
                                                    }
                                                    $base64Image = base64_encode($imageData);

                                                    return [
                                                        'type' => 'image_url',
                                                        'image_url' => [
                                                            'url' => 'data:image/png;base64,'.$base64Image,
                                                        ],
                                                    ];
                                                })->toArray()
                                            ),
                                        ],
                                    ],
                                    'max_tokens' => 2000,
                                    'stream' => true,
                                ],
                            ],
                        );
                    } catch (\Exception $exception) {
                        $messageError = 'Error from API call. Please try again. If error persists again please contact system administrator with this message '.$exception->getMessage();
                        echo "data: $messageError";
                        echo "\n\n";
                        //ob_flush();
                        flush();
                        echo 'data: [DONE]';
                        echo "\n\n";
                        //ob_flush();
                        flush();
                        usleep(50000);
                    }

                    $total_used_tokens = 0;
                    $output = '';
                    $responsedText = '';

                    foreach (explode("\n", $response->getBody()->getContents()) as $chunk) {
                        if (strlen($chunk) > 5 && $chunk != 'data: [DONE]' && isset(json_decode(substr($chunk, 6, strlen($chunk) - 6))->choices[0]->delta->content)) {

                            $message = json_decode(substr($chunk, 6, strlen($chunk) - 6))->choices[0]->delta->content;

                            $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);
                            $output .= $messageFix;

                            $responsedText .= $message;
                            $total_used_tokens += countWords($message);

                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);

                            echo PHP_EOL;
                            echo 'data: '.$messageFix.'/**'.$random_text."\n\n";
                            //ob_flush();
                            flush();
                            usleep(5000);

                            // echo "event: data\n";
                            // echo "data: " . json_encode(['message' => json_decode(substr($chunk, 6, strlen($chunk) - 6))->choices[0]->delta->content]) . "\n\n";
                            // flush();
                        }
                    }
                }
                $message = UserOpenaiChatMessage::whereId($message_id)->first();
                $chat = UserOpenaiChat::whereId($chat_id)->first();
                $message->response = $responsedText;
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $message->images = implode(',', $images);
                $message->pdfName = $request->pdfname;
                $message->pdfPath = $request->pdfpath;
                $message->save();

                $user = Auth::user();

                if ($user->remaining_words != -1) {
                    $user->remaining_words -= $total_used_tokens;
                }

                if ($user->remaining_words < -1) {
                    $user->remaining_words = 0;
                }
                $user->save();

                $chat->total_credits += $total_used_tokens;
                $chat->save();
                echo 'data: [DONE]';
                echo "\n\n";
                //ob_flush();
                flush();
                usleep(50000);
            }, 200, [
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
                'Content-Type' => 'text/event-stream',
            ]);
        } else {

            // Check RateLimit
            $ipAddress = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : request()->ip();
            $db_ip_address = RateLimit::where('ip_address', $ipAddress)->where('type', 'chatbot')->first();
            if ($db_ip_address) {
                if (now()->diffInDays(Carbon::parse($db_ip_address->last_attempt_at)->format('Y-m-d')) > 0) {
                    $db_ip_address->attempts = 0;
                }
            } else {
                $db_ip_address = new RateLimit(['ip_address' => $ipAddress, 'type' => 'chatbot']);
            }

            if ($db_ip_address->attempts >= $settings2->chatbot_rate_limit) {
                $data = [
                    'errors' => [__('You have reached the maximum number of ask to chatbot allowed.')],
                ];

                return response()->json($data, 429);
            } else {
                $db_ip_address->attempts++;
                $db_ip_address->last_attempt_at = now();
                $db_ip_address->save();
            }

            $chat = UserOpenaiChat::where('id', $request->chat_id)->first();
            $category = OpenaiGeneratorChatCategory::where('id', $request->category_id)->first();
            $realtime = $request->realtime;

            $user = Auth::user();
            if ($user->remaining_words != -1) {
                if ($user->remaining_words <= 0) {
                    $data = [
                        'errors' => ['You have no credits left. Please consider upgrading your plan.'],
                    ];

                    return response()->json($data, 419);
                }
            }

            $total_used_tokens = 0;

            $entry = new UserOpenaiChatMessage();
            $entry->user_id = Auth::id();
            $entry->user_openai_chat_id = $chat->id;
            $entry->is_chatbot = 1;
            $entry->input = $request->prompt;
            $entry->response = null;
            $entry->realtime = $realtime ?? 0;
            $entry->output = "(If you encounter this message, please attempt to send your message again. If the error persists beyond multiple attempts, please don't hesitate to contact us for assistance!)";
            $entry->hash = Str::random(256);
            $entry->credits = $total_used_tokens;
            $entry->words = 0;
            $entry->save();

            $user->save();

            $chat->total_credits += $total_used_tokens;
            $chat->save();

            $chat_id = $chat->id;
            $message_id = $entry->id;

            return response()->json(compact('chat_id', 'message_id'));
        }
    }

    public function transAudio(Request $request)
    {
        $user = Auth::user();
        $file = $request->file('file');
        $path = 'upload/audio/';

        $file_name = Str::random(4).'-'.Str::slug($user->fullName()).'-audio.'.$file->getClientOriginalExtension();

        //Audio Extension Control
        $imageTypes = ['mp3', 'mp4', 'mpeg', 'mpga', 'm4a', 'wav', 'webm'];
        if (! in_array(Str::lower($file->getClientOriginalExtension()), $imageTypes)) {
            $data = [
                'errors' => ['Invalid extension, accepted extensions are mp3, mp4, mpeg, mpga, m4a, wav, and webm.'],
            ];

            return response()->json('', 419);
        }

        try {

            $file->move($path, $file_name);

            $response = OpenAI::audio()->transcribe([
                'file' => fopen($path.$file_name, 'r'),
                'model' => 'whisper-1',
                'response_format' => 'verbose_json',
            ]);

            unlink($path.$file_name);
            $text = $response->text;
        } catch (\Exception $e) {
            $text = '';
        }

        return response()->json($text);
    }

    public function deleteChat(Request $request)
    {
        $chat_id = explode('_', $request->chat_id)[1];

        $chat = UserOpenaiChat::where('id', $chat_id)->first();

        $chat->delete();
    }

    public function renameChat(Request $request)
    {
        $chat_id = explode('_', $request->chat_id)[1];
        $chat = UserOpenaiChat::where('id', $chat_id)->first();
        $chat->title = $request->title;
        $chat->save();
    }

    //Low
    public function lowChatSave(Request $request)
    {
        $chat = UserOpenaiChat::where('id', $request->chat_id)->first();

        $message = new UserOpenaiChatMessage();
        $message->user_openai_chat_id = $chat->id;
        $message->user_id = Auth::id();
        $message->input = $request->input;
        $message->response = $request->response;
        $message->output = $request->response;
        $message->hash = Str::random(256);
        $message->credits = countWords($request->response);
        $message->words = countWords($request->response);
        $message->images = $request->images;
        $message->pdfPath = $request->pdfPath;
        $message->pdfName = $request->pdfName;
        $message->outputImage = $request->outputImage;
        $message->save();

        /**
         * @var \App\Models\User
         */
        $user = Auth::user();

        $total_used_tokens = $message->credits;

        userCreditDecreaseForWord($user, $total_used_tokens);

        return response()->json([]);
    }
}
