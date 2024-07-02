<?php

namespace App\Services\Ai;

use App\Helpers\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Anthropic
{
    public ?string $key = null;

    public bool $stream = false;

    public array $messages = [];

    public ?string $system = null;

    public function stream($chat_bot, $extra_prompt = ''): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        $client = $this->client();
        $system = (bool) $this->system;
        // Log::info('$extra_prompt');
        // Log::info(serialize($extra_prompt));
        // Log::info('$this->messages');
        // Log::info(serialize($this->messages));
        // Log::info('$this->system');
        // Log::info('$this->system.$extra_prompt');
        // Log::info( $this->system.$extra_prompt);
        $body = Helper::arrayMerge($system, [
            'model' => $chat_bot,
            'max_tokens' => (int) setting('anthropic_max_output_length', 4096),
            'messages' => $this->messages,
            'stream' => $this->isStream()
        ],[
            'system' => $this->system.$extra_prompt,
        ]);

        return $client

        ->post('https://api.anthropic.com/v1/messages', $body);
    }

    public function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders([
            'x-api-key' => $this->getKey(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01'
        ]);
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key ?? Helper::setAnthropicKey();
    }

    public function isStream(): bool
    {
        return $this->stream;
    }

    public function setStream(bool $stream): self
    {
        $this->stream = $stream;

        return $this;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function setMessages(array $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    public function setSystem(?string $system): self
    {
        $this->system = $system;

        return $this;
    }
}