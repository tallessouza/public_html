<?php

namespace App\Services\Ai;

use App\Helpers\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\Ai\Traits\StreamResponse;

class Gemini
{

    public array $history = [];
	public $api = "https://generativelanguage.googleapis.com/v1beta/models/";
	public $streamEndpoint = ":streamGenerateContent";
	public $contentEndpoint = ":generateContent";

	public function streamGenerateContent($model = 'gemini-pro'): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        $client = $this->client();
        $body = [
			'contents' => $this->getHistory()
		];
		$url = $this->api . $model .$this->streamEndpoint. "?key=" . config('gemini.api_key');
        return $client->withOptions(['stream' => true])->post($url, $body);
    }
	public function generateContent($model = 'gemini-pro'){
		$client = $this->client();
        $body = [
			'contents' => $this->getHistory()
		];
		$url = $this->api . $model .$this->contentEndpoint. "?key=" . config('gemini.api_key');
        return $client->post($url, $body);
	}

    public function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }

	/**
     * Read a line from the stream.
     */
    public function readLine($stream): string
    {
        $buffer = '';

        while (! $stream->eof()) {
            $buffer .= $stream->read(1);

            if (strlen($buffer) === 1 && $buffer !== '{') {
                $buffer = '';
            }

            if (json_decode($buffer) !== null) {
                return $buffer;
            }
        }

        return rtrim($buffer, ']');
    }

    public function getHistory(): array
    {
        return $this->history;
    }

    public function setHistory(array $history): self
    {
        $this->history = $history;

        return $this;
    }

}