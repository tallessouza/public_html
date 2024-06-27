<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    protected $apiKey;
    protected $baseUrl;
    protected $mainInstance;
    protected $webHook;

    public function __construct()
    {
        $this->apiKey = env('EVO_APIKEY');
        $this->baseUrl = env('EVO_URL');
        $this->mainInstance = env('EVO_MAININSTANCE');
        $this->webHook = env('EVO_WEBHOOK');
    }

    public function checkInstanceState()
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey
            ])->get("{$this->baseUrl}/instance/connectionState/{$this->mainInstance}");

            if ($response->successful()) {
                $data = $response->json();
                return isset($data['instance']) && $data['instance']['state'] === 'open';
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    public function isWhatsAppNumber($phone)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'apikey' => $this->apiKey
        ])->post("{$this->baseUrl}/chat/whatsappNumbers/{$this->mainInstance}", [
            'numbers' => [$phone]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return isset($data[0]['exists']) && $data[0]['exists'];
        }

        return false;
    }

    public function fetchInstance($instanceName)
    {
        $response = Http::withHeaders([
            'apikey' => $this->apiKey
        ])->get("{$this->baseUrl}/instance/fetchInstances", [
            'instanceName' => $instanceName
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function connectInstance($instanceName)
    {
        $response = Http::withHeaders([
            'apikey' => $this->apiKey
        ])->get("{$this->baseUrl}/instance/connect/{$instanceName}");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function createInstance($instanceName, $number)
    {
        $response = Http::withHeaders([
            'apikey' => $this->apiKey
        ])->post("{$this->baseUrl}/instance/create", [
            "instanceName" => $instanceName,
            "token" => "",
            "qrcode" => true,
            "number" => $number,
            "webhook" => $this->webHook,
            "webhook_by_events" => false,
            "webhook_base64" => true,
            "events" => [
                "MESSAGES_UPSERT"
            ]
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
    public function configureRabbitMQ($instanceName)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'apikey' => $this->apiKey
        ])->post("{$this->baseUrl}/rabbitmq/set/{$instanceName}", [
            'enabled' => true,
            'events' => [
                'MESSAGES_UPSERT'
            ]
        ]);

        if (!$response->successful()) {
            // \Log::error("Falha ao configurar RabbitMQ para a instância {$instanceName}");
            return false;
        }

        return true;
    }
    public function logoutInstance($instanceName)
    {
        $response = Http::withHeaders([
            'apikey' => $this->apiKey
        ])->delete("{$this->baseUrl}/instance/logout/{$instanceName}");

        return $response->successful();
    }
}