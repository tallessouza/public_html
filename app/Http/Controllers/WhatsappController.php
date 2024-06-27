<?php

namespace App\Http\Controllers;

use App\Services\WhatsappService;
use Illuminate\Support\Facades\Auth;

class WhatsappController extends Controller
{
    protected $whatsappService;

    public function __construct()
    {
        $this->whatsappService = new WhatsappService();
    }

    public function index()
    {
        $user = Auth::user();
        $phone = $user->phone;

        // Validação do telefone usando regex
        $validphone = preg_match('/^\+\d{1,3}\d{1,14}(?:x.+)?$/', $phone);

        $qrCodeBase64 = '';
        $invalidphone = false;
        $serverError = false;
        $connected = false;

        // Verificar o estado da instância
        if (!$this->whatsappService->checkInstanceState()) {
            $serverError = true;
            return view('default.panel.user.whatsapp.index', compact('qrCodeBase64', 'invalidphone', 'serverError', 'connected'));
        }

        if ($validphone) {
            // Remover o caractere '+' do número de telefone
            $phoneWithoutPlus = str_replace('+', '', $phone);

            try {
                // Verificar se o número é um número do WhatsApp
                if ($this->whatsappService->isWhatsAppNumber($phoneWithoutPlus)) {
                    // Número é um número do WhatsApp, verificar a instância
                    $fetchData = $this->whatsappService->fetchInstance($phoneWithoutPlus);

                    if ($fetchData && isset($fetchData['instance'])) {
                        // Instância já existe, verificar o estado de conexão
                        $connectData = $this->whatsappService->connectInstance($phoneWithoutPlus);

                        if ($connectData && isset($connectData['instance']) && $connectData['instance']['state'] === 'open') {
                            // Instância já está conectada, não precisa de QR code
                            $connected = true;
                            $qrCodeBase64 = '';
                        } else {
                            // Instância não está conectada, obter o QR code
                            $qrCodeBase64 = $connectData['base64'] ?? '';
                        }
                    } else {
                        // Criar nova instância
                        $createData = $this->whatsappService->createInstance($phoneWithoutPlus, $phoneWithoutPlus);

                        if ($createData) {
                            $qrCodeBase64 = $createData['qrcode']['base64'];
                            
                            // Configurar RabbitMQ para a nova instância
                            $this->whatsappService->configureRabbitMQ($phoneWithoutPlus);
                        } else {
                            $serverError = true;
                        }
                    }
                } else {
                    // Número não é um número do WhatsApp
                    $invalidphone = true;
                }
            } catch (\Exception $e) {
                // Captura qualquer exceção e define a variável de erro do servidor
                $serverError = true;
            }
        } else {
            // Número de telefone inválido
            $invalidphone = true;
        }

        return view('default.panel.user.whatsapp.index', compact('qrCodeBase64', 'invalidphone', 'serverError', 'connected'));
    }

    public function logout()
    {
        $user = Auth::user();
        $phone = $user->phone;

        // Remover o caractere '+' do número de telefone
        $phoneWithoutPlus = str_replace('+', '', $phone);

        $logoutSuccess = false;
        $serverError = false;

        try {
            $logoutSuccess = $this->whatsappService->logoutInstance($phoneWithoutPlus);
        } catch (\Exception $e) {
            $serverError = true;
        }

        return redirect()->route('dashboard.user.whatsapp');
    }

    public function regenerate()
    {
        $user = Auth::user();
        $phone = $user->phone;

        // Remover o caractere '+' do número de telefone
        $phoneWithoutPlus = str_replace('+', '', $phone);

        try {
            $this->whatsappService->logoutInstance($phoneWithoutPlus);
        } catch (\Exception $e) {
            // Captura qualquer exceção e define a variável de erro do servidor
        }

        return redirect()->route('dashboard.user.whatsapp');
    }

    public function checkConnection()
    {
        $user = Auth::user();
        $phone = $user->phone;
        $phoneWithoutPlus = str_replace('+', '', $phone);

        try {
            $connectData = $this->whatsappService->connectInstance($phoneWithoutPlus);
            if ($connectData && isset($connectData['instance']) && $connectData['instance']['state'] === 'open') {
                return response()->json(['connected' => true]);
            }
        } catch (\Exception $e) {
            return response()->json(['connected' => false]);
        }

        return response()->json(['connected' => false]);
    }
}