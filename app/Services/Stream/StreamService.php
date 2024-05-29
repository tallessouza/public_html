<?php

namespace App\Services\Stream;

use GuzzleHttp\Client;
use App\Helpers\Classes\Helper;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\UserOpenaiChat;
use Illuminate\Support\Facades\Auth;
use App\Models\UserOpenaiChatMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\UserOpenai;
use App\Services\Ai\Anthropic;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Services\Ai\Gemini;

class StreamService
{
    public function __construct()
    {
		switch (setting('default_ai_engine', 'openai'))
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
    }
	public function ChatStream($chat_bot, $history, $main_message, $chat_type , $contain_images){
		switch (setting('default_ai_engine', 'openai'))
		{
			case 'openai':
				return $this->openaiChatStream($chat_bot, $history, $main_message, $chat_type , $contain_images);
			case 'anthropic':
				return $this->anthropicChatStream($chat_bot, $history, $main_message, $chat_type , $contain_images);
			case 'gemini':
				return $this->geminiChatStream($chat_bot, $history, $main_message, $chat_type , $contain_images);
			default:
				return $this->openaiChatStream($chat_bot, $history, $main_message, $chat_type , $contain_images);
		}
	}
	public function OtherStream(Request $request, $chat_bot){
		switch (setting('default_ai_engine', 'openai'))
		{
			case 'openai':
				return $this->openaiOtherStream($request, $chat_bot);
			case 'anthropic':
				return $this->anthropicOtherStream($request, $chat_bot);
			case 'gemini':
				return $this->geminiOtherStream($request, $chat_bot);
			default:
				return $this->openaiOtherStream($request, $chat_bot);
		}
	}
	public function reduceTokensWhenIntterruptStream(Request $request, $type) {
		if($type == 'writer') {
			$streamed_text = $request->get('streamed_text');
			$message_id = $request->get('streamed_message_id');
			if($streamed_text) {
				$total_used_tokens = countWords($streamed_text);
				$user = Auth::user();
				userCreditDecreaseForWord($user, $total_used_tokens);
				if ($message_id != '' && $message_id != null && $message_id != 0) {
					$entry = UserOpenai::find($message_id);
					if($entry != null){
						$entry->title = __('New Workbook');
						$entry->credits = $total_used_tokens;
						$entry->words = $total_used_tokens;
						$entry->response = $streamed_text;
						$entry->output = $streamed_text;
						$entry->save();
					}
				}
			}
		} else { // chat
			$streamed_text = $request->get('streamed_text');
			$message_id = $request->get('streamed_message_id');
			if($streamed_text) {
				$total_used_tokens = countWords($streamed_text);
				$user = Auth::user();
				userCreditDecreaseForWord($user, $total_used_tokens);
				if ($message_id != '' && $message_id != null && $message_id != 0) {
					$main_message = UserOpenaiChatMessage::find($message_id);
					if($main_message){
						$chat_id = $main_message->user_openai_chat_id;
						$chat = UserOpenaiChat::whereId($chat_id)->first();
						$main_message->response = $streamed_text;
						$main_message->output = $streamed_text;
						$main_message->credits = $total_used_tokens;
						$main_message->words = $total_used_tokens;
						$main_message->save();
						$chat->total_credits += $total_used_tokens;
						$chat->save();
					}
				}
			}
		}
	}

	# OpenAI Stream
  	private function openaiChatStream($chat_bot, $history, $main_message, $chat_type , $contain_images) {
		$total_used_tokens = 0;
		$output = '';
		$responsedText = '';
        return response()->stream(function () use ($chat_bot, $history, &$total_used_tokens, &$output, &$responsedText, $main_message, $contain_images) {
			$chat_id = $main_message->user_openai_chat_id;
			$chat = UserOpenaiChat::whereId($chat_id)->first();

			echo "event: message\n";
			echo 'data: ' . $main_message->id . "\n\n";

			if(!$contain_images) {
				// Log::info('OpenAI Chat Stream');
				$stream = OpenAI::chat()->createStreamed([
					'model' => $chat_bot,
					'messages' => $history,
					'temperature' => 1.0,
                    'frequency_penalty' => 0,
                    'presence_penalty' => 0,
					'stream' => true
				]);
			} else {
				// Log::info('OpenAI Chat Stream with images');
				$stream = OpenAI::chat()->createStreamed([
					'model' => 'gpt-4-vision-preview',
					'messages' => $history,
					'max_tokens' => 2000,
					'temperature' => 1.0,
                    'frequency_penalty' => 0,
                    'presence_penalty' => 0,
					'stream' => true
				]);
			}
			foreach ($stream as $response) {
				if (isset($response->choices[0]->delta->content)) {
					$text = $response->choices[0]->delta->content;
					$messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $text);
					$output .= $messageFix;
					$responsedText .= $text;
					$total_used_tokens += countWords($text);
					if (connection_aborted()) {
						break;
					}
					echo PHP_EOL;
					echo "event: data\n";
					echo 'data: ' . $messageFix;
					echo "\n\n";
					flush();
				}
			}
            echo "event: stop\n";
            echo 'data: [DONE]';
            echo "\n\n";
            flush();


			$main_message->response = $responsedText;
			$main_message->output = $output;
			$main_message->credits = $total_used_tokens;
			$main_message->words = $total_used_tokens;
			$main_message->save();
			$user = Auth::user();
			userCreditDecreaseForWord($user, $total_used_tokens);
			$chat->total_credits += $total_used_tokens;
			$chat->save();
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

	private function openaiOtherStream(Request $request, $chat_bot) {
		$prompt = $request->get('prompt');
		$message_id = $request->get('message_id');
		$openai_id = $request->get('openai_id');
		$title = $request->get('title');

		$history[] = ['role' => 'user', 'content' => $prompt];
		$total_used_tokens = 0;
		$output = '';
		$responsedText = '';
        return response()->stream(function () use ($chat_bot, $history, &$total_used_tokens, &$output, &$responsedText, $message_id, $title, $openai_id, $prompt) {
			$user = Auth::user();
			$entry = UserOpenai::find($message_id);
			if($entry == null){
				$entry = new UserOpenai();
				$entry->user_id = $user->id;
				$entry->input =  $prompt;
				$entry->hash = str()->random(256);
				$entry->team_id = $user->team_id;
				$entry->slug = str()->random(7).str($user->fullName())->slug().'-workbook';
				$entry->openai_id = $openai_id ?? 1;
			}

			echo "event: message\n";
			echo 'data: ' . $message_id . "\n\n";


			$stream = OpenAI::chat()->createStreamed([
				'model' => $chat_bot,
				'messages' => $history,
				'temperature' => 1.0,
				'frequency_penalty' => 0,
				'presence_penalty' => 0,
				'stream' => true
			]);

			foreach ($stream as $response) {
				if (isset($response->choices[0]->delta->content)) {
					$text = $response->choices[0]->delta->content;
					$messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $text);
					$output .= $messageFix;
					$responsedText .= $text;
					$total_used_tokens += countWords($text);
					if (connection_aborted()) {
						break;
					}
					echo PHP_EOL;
					echo "event: data\n";
					echo 'data: ' . $messageFix;
					echo "\n\n";
					flush();
				}
			}
            echo "event: stop\n";
            echo 'data: [DONE]';
            echo "\n\n";
            flush();


			$entry->title = $title ?: __('New Workbook');
			$entry->credits = $total_used_tokens;
			$entry->words = $total_used_tokens;
			$entry->response = $responsedText;
			$entry->output = $output;
			$entry->save();
			userCreditDecreaseForWord($user, $total_used_tokens);
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
	}
	
	# Anthropic Stream
	private function anthropicChatStream($chat_bot, $history, $main_message, $chat_type , $contain_images) {
		$total_used_tokens = 0;
		$output = '';
		$responsedText = '';
		$client = app(Anthropic::class);
        return response()->stream(function () use ($chat_bot,$client ,$history, &$total_used_tokens, &$output, &$responsedText, $main_message, $contain_images) {
			$chat_id = $main_message->user_openai_chat_id;
			$chat = UserOpenaiChat::whereId($chat_id)->first();

			echo "event: message\n";
			echo 'data: ' . $main_message->id . "\n\n";

			if(!$contain_images) {
				// Log::info('Anthropic Chat Stream');
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

						echo PHP_EOL;
						echo "event: data\n";
						echo 'data: ' . $messageFix;
						echo "\n\n";
						flush();
					}
					if (connection_aborted()) {
						break;
					}
				}
			} else {
				// Log::info('Anthropic Chat Stream with images');
				Helper::setOpenAiKey();
				$stream = OpenAI::chat()->createStreamed([
					'model' => 'gpt-4-vision-preview',
					'messages' => $history,
					'max_tokens' => 2000,
					'temperature' => 1.0,
                    'frequency_penalty' => 0,
                    'presence_penalty' => 0,
					'stream' => true
				]);
				foreach ($stream as $response) {
					if (isset($response->choices[0]->delta->content)) {
						$text = $response->choices[0]->delta->content;
						$messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $text);
						$output .= $messageFix;
						$responsedText .= $text;
						$total_used_tokens += countWords($text);
						if (connection_aborted()) {
							break;
						}
						echo PHP_EOL;
						echo "event: data\n";
						echo 'data: ' . $messageFix;
						echo "\n\n";
						flush();
					}
				}
			}

            echo "event: stop\n";
            echo 'data: [DONE]';
            echo "\n\n";
            flush();

			$main_message->response = $responsedText;
			$main_message->output = $output;
			$main_message->credits = $total_used_tokens;
			$main_message->words = $total_used_tokens;
			$main_message->save();
			$user = Auth::user();
			userCreditDecreaseForWord($user, $total_used_tokens);
			$chat->total_credits += $total_used_tokens;
			$chat->save();
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

	private function anthropicOtherStream(Request $request, $chat_bot) {
		$prompt = $request->get('prompt');
		$message_id = $request->get('message_id');
		$openai_id = $request->get('openai_id');
		$title = $request->get('title');

		$history[] = ['role' => 'user', 'content' => $prompt];
		$total_used_tokens = 0;
		$output = '';
		$responsedText = '';

        return response()->stream(function () use ($chat_bot,$history, &$total_used_tokens, &$output, &$responsedText, $message_id, $title, $openai_id, $prompt) {
			$user = Auth::user();
			$entry = UserOpenai::find($message_id);
			if($entry == null){
				$entry = new UserOpenai();
				$entry->user_id = $user->id;
				$entry->input =  $prompt;
				$entry->hash = str()->random(256);
				$entry->team_id = $user->team_id;
				$entry->slug = str()->random(7).str($user->fullName())->slug().'-workbook';
				$entry->openai_id = $openai_id ?? 1;
			}

			echo "event: message\n";
			echo 'data: ' . $message_id . "\n\n";

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

					echo PHP_EOL;
					echo "event: data\n";
					echo 'data: ' . $messageFix;
					echo "\n\n";
					flush();
				}
				if (connection_aborted()) {
					break;
				}
			}
            echo "event: stop\n";
            echo 'data: [DONE]';
            echo "\n\n";
            flush();

			$entry->title = $title ?: __('New Workbook');
			$entry->credits = $total_used_tokens;
			$entry->words = $total_used_tokens;
			$entry->response = $responsedText;
			$entry->output = $output;
			$entry->save();
			userCreditDecreaseForWord($user, $total_used_tokens);
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
	}

	# Gemini Stream
	private function geminiChatStream($chat_bot, $history, $main_message, $chat_type , $contain_images){
		$total_used_tokens = 0;
		$output = '';
		$responsedText = '';
		$newhistory = convertHistoryToGemini($history);

        if ($contain_images)
        {
            # I will improve later
            $newhistory = $this->getLastMessageAndImage($newhistory);

            if (count($newhistory['parts']) == 1) {
                $newhistory['parts'][0] = [
                    'text' => $newhistory['parts'][0]['text']
                ];

                $contain_images = false;
            }

            $newhistory = [$newhistory];
        }


        return response()->stream(function () use ($chat_bot, $newhistory, &$total_used_tokens, &$output, &$responsedText, $main_message, $contain_images) {
			$chat_id = $main_message->user_openai_chat_id;
			$chat = UserOpenaiChat::whereId($chat_id)->first();
			echo "event: message\n";
			echo 'data: ' . $main_message->id . "\n\n";
			
			if($contain_images)
            {
				$chat_bot = 'gemini-pro-vision';
			}

			$client = app(Gemini::class);
			$response = $client
			->setHistory($newhistory)
			->streamGenerateContent($chat_bot);

			while (!$response->getBody()->eof()) {
            
				$line = $client->readLine($response->getBody());
				$decodedLine = json_decode($line, true);
									
				if ($decodedLine === null || !isset($decodedLine['candidates'])) {
					continue;
				}
				
				foreach ($decodedLine['candidates'] as $candidate) {
					$text = $candidate['content']['parts'][0]['text'];
					$messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $text);
					$output .= $messageFix;
					$responsedText .= $text;
					$total_used_tokens += countWords($text);
					if (connection_aborted()) {
						break;
					}
					echo PHP_EOL;
					echo "event: data\n";
					echo 'data: ' . $messageFix;
					echo "\n\n";
					flush();
				}
			}

            echo "event: stop\n";
            echo 'data: [DONE]';
            echo "\n\n";
            flush();

			$main_message->response = $responsedText;
			$main_message->output = $output;
			$main_message->credits = $total_used_tokens;
			$main_message->words = $total_used_tokens;
			$main_message->save();
			$user = Auth::user();
			userCreditDecreaseForWord($user, $total_used_tokens);
			$chat->total_credits += $total_used_tokens;
			$chat->save();

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
	}

    public function getLastMessageAndImage($newhistory)
    {
        return Arr::last($newhistory);
    }

    private function geminiOtherStream(Request $request, $chat_bot) {

        $prompt = $request->get('prompt');
        $message_id = $request->get('message_id');
        $openai_id = $request->get('openai_id');
        $title = $request->get('title');

        $history[] = [
            'parts' => [
                [
                    'text' => $prompt
                ]
            ],
            'role' => 'user'
        ];

        $total_used_tokens = 0;
        $output = '';
        $responsedText = '';
        return response()->stream(function () use ($chat_bot, $history, &$total_used_tokens, &$output, &$responsedText, $message_id, $title, $openai_id, $prompt) {
            $user = Auth::user();
            $entry = UserOpenai::find($message_id);
            if($entry == null){
                $entry = new UserOpenai();
                $entry->user_id = $user->id;
                $entry->input =  $prompt;
                $entry->hash = str()->random(256);
                $entry->team_id = $user->team_id;
                $entry->slug = str()->random(7).str($user->fullName())->slug().'-workbook';
                $entry->openai_id = $openai_id ?? 1;
            }

            echo "event: message\n";
            echo 'data: ' . $message_id . "\n\n";


            $client = app(Gemini::class);
            $response = $client
                ->setHistory($history)
                ->streamGenerateContent($chat_bot);

            while (!$response->getBody()->eof()) {

                $line = $client->readLine($response->getBody());
                $decodedLine = json_decode($line, true);

                if ($decodedLine === null || !isset($decodedLine['candidates'])) {
                    continue;
                }

                foreach ($decodedLine['candidates'] as $candidate) {
                    $text = $candidate['content']['parts'][0]['text'];
                    $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $text);
                    $output .= $messageFix;
                    $responsedText .= $text;
                    $total_used_tokens += countWords($text);
                    if (connection_aborted()) {
                        break;
                    }
                    echo PHP_EOL;
                    echo "event: data\n";
                    echo 'data: ' . $messageFix;
                    echo "\n\n";
                    flush();
                }
            }

            echo "event: stop\n";
            echo 'data: [DONE]';
            echo "\n\n";
            flush();


            $entry->title = $title ?: __('New Workbook');
            $entry->credits = $total_used_tokens;
            $entry->words = $total_used_tokens;
            $entry->response = $responsedText;
            $entry->output = $output;
            $entry->save();
            userCreditDecreaseForWord($user, $total_used_tokens);
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

}