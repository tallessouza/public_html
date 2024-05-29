<?php

namespace App\Services\Ai;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoStudioService
{
    public string $action;

    public ?UploadedFile $photo = null;

    public string $baseUrl = 'https://clipdrop-api.co/';

    public function generate(): bool|string
    {
        $action = match ($this->getAction()) {
            'reimagine' => $this->reimagine(),
            'remove_background' => $this->remove_background(),
            'replace_background' => $this->replace_background(),
            'sketch_to_image' => $this->sketch_to_image(),
            'text_to_image' => $this->text_to_image(),
            'upscale' => $this->upscale(),
            'remove_text' => $this->remove_text(),
            default => [],
        };

        if ($action) {
            $fileName = 'photo-studio/'.Str::random('36').'.jpg';
            Storage::disk('public')
                ->put(
                    $fileName,
                    $action->body()
                );

            return $fileName;
        }

        return false;
    }

    public function reimagine()
    {
        return $this->request(
            $this->baseUrl.'reimagine/v1/reimagine'
        );
    }

    public function remove_text()
    {
        return $this->request(
            $this->baseUrl.'remove-text/v1'
        );
    }

    public function upscale()
    {
        return $this->request(
            $this->baseUrl.'image-upscaling/v1/upscale', [
            'target_width' => '2048',
            'target_height' => '2048',
        ]);
    }

    public function remove_background()
    {
        return $this->request(
            $this->baseUrl.'remove-background/v1', [], 'image_file', true);
    }

    public function replace_background()
    {
        return $this->request(
            $this->baseUrl.'replace-background/v1', [
            'prompt' => request('description'),
        ]);
    }

    public function sketch_to_image()
    {
        return $this->request(
            $this->baseUrl.'sketch-to-image/v1/sketch-to-image', [
            'prompt' => request('description'),
        ], 'sketch_file');
    }

    public function text_to_image()
    {
        return $this->request(
            $this->baseUrl.'text-to-image/v1', [
            'prompt' => request('description'),
        ], 'sketch_file', false);
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getPhoto(): UploadedFile
    {
        return $this->photo;
    }

    public function setPhoto(?UploadedFile $photo = null): PhotoStudioService
    {
        $this->photo = $photo;

        return $this;
    }

    public function request(string $url, array $data = [], $fileKey = 'image_file', $hasFile = true)
    {
        $http = Http::asMultipart()
            ->withHeaders([
                'x-api-key' => $this->getApiKey(),
            ])
            ->when($hasFile, function ($http) use ($fileKey) {
                return $http->attach($fileKey, $this->getPhoto()->getContent(), $this->getPhoto()->getClientOriginalName());
            })
            ->post($url, $data);

        if ($http->successful()) {
            return $http;
        }

        return false;
    }

    public function getApiKey(): string
    {
        return setting('clipdrop_api_key', '');
    }
}
