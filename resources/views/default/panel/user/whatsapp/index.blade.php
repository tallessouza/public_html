@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('WhatsApp'))
@section('titlebar_title')
{{ __('WhatsApp') }}
@endsection
@section('additional_css')
<style>
    .checkmark {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: block;
        stroke-width: 2;
        stroke: #228F75;
        stroke-miterlimit: 10;
        box-shadow: inset 0px 0px 0px #228F75;
        animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        position: relative;
        top: 5px;
        right: 5px;
        margin: 0 auto;
    }

    .checkmark__circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 2;
        stroke-miterlimit: 10;
        stroke: #228F75;
        fill: transparent;
        animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }

    .checkmark__check {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
    }

    @keyframes stroke {
        100% {
            stroke-dashoffset: 0;
        }
    }

    @keyframes scale {

        0%,
        100% {
            transform: none;
        }

        50% {
            transform: scale3d(1.1, 1.1, 1);
        }
    }

    @keyframes fill {
        100% {
            box-shadow: inset 0px 0px 0px 4px #228F75;
        }
    }
</style>
@endsection
@section('content')

@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

@if ($serverError)
<x-card class:body="mt-4 flex flex-wrap justify-between" size="lg">
    <div class="mb-4 md:mb-0 lg:w-2/5">
        <h3 class="mb-7">{{ __('Erro no Servidor!') }}</h3>

        <p class="mb-3">
            Ocorreu um erro no servidor. Por favor, tente novamente mais tarde.
        </p>
    </div>
</x-card>
@elseif ($invalidphone)
<x-card class:body="mt-4 flex flex-wrap justify-between" size="lg">
    <div class="mb-4 md:mb-0 ">
        <h3 class="mb-7">{{ __('Número de telefone inválido!') }}</h3>

        <p class="mb-3">
            Para utilizar a transcrição de áudios, seu número do Whatsapp deve estar configurado no seu perfil.
        </p>
        <p class="mb-3">
            O número deve estar no padrão DDI (+55), DDD (21), número (988888888) e ter uma conta ativa no Whatsapp.
            Exemplo: +5521988888888
        </p>
        <p class="mb-3">
            Clique no botão abaixo para configurá-lo.
        </p>

        <div class="flex flex-wrap items-center gap-4">
            <x-button class="hover:bg-primary" variant="ghost-shadow" href="{{ route('dashboard.user.settings.index') }}">
                <x-tabler-plus class="size-4" />
                {{ __('Configurar número de telefone.') }}
            </x-button>
        </div>
    </div>
</x-card>
@else
<div class="flex flex-wrap justify-center gap-8 pt-10 ">
    <div class="py-10 mx-auto text-center">
        @if ($qrCodeBase64)
        <div class="[&_.tox-edit-area__iframe]:!bg-transparent">
            <figure>
                <img id="qrCodeImage" class="rounded-xl shadow-xl mx-auto" src="{{ $qrCodeBase64 }}" alt="QR Code" />
            </figure>
            <p id="qrCodeMessage" class="mb-3 mt-4 text-center">
                Leia o QR Code para ativar o serviço.
            </p> 
            <p id="qrCodeMessage2" class="mb-3 mt-4 text-center">
                Ao conectar seu Whatsapp, você entende que: <br>1 - as palavras transcritas serão debitados do seu saldo; <br>2 - o uso de serviços não oficiais pode gerar banimento da sua conta no Whatsapp e você se responsabiliza por isso.
            </p>
        </div>
        <div class="flex justify-center">
            <form id="regenerateForm" method="POST" action="{{ route('dashboard.user.whatsapp.regenerate') }}">
                @csrf
                <x-button class="mt-4 flex max-xl:hidden" type="submit">
                    <span class="max-lg:hidden">
                        {{ __('Gerar novamente') }}
                    </span>
                </x-button>
            </form>
        </div>
        @endif
        <div id="successMessage" class="py-10" style="display: {{ $connected ? 'block' : 'none' }};">
            <div class="container-xl">
                <div class="success-animation mb-3">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                    </svg>
                </div>
                <div class="text-center">
                    <h1 class="text-heading text-center">{{ __('Conexão bem-sucedida') }}</h1>
                    <p class="text-heading my-3 text-center">
                        {{ __('Seu número de WhatsApp está conectado com sucesso. Agora você pode usar nossos serviços.') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="flex justify-center">
            <form id="disconnectForm" method="POST" action="{{ route('dashboard.user.whatsapp.logout') }}" style="display: {{ $connected ? 'block' : 'none' }};">
                @csrf
                <x-button class="mt-4 flex max-xl:hidden" type="submit">
                    <span class="max-lg:hidden">
                        {{ __('Desconectar') }}
                    </span>
                </x-button>
            </form>
        </div>
    </div>
</div>

@endif

<script>
    function checkConnection() {
        fetch('{{ route('dashboard.user.whatsapp.checkConnection') }}')
            .then(response => response.json())
            .then(data => {
                if (data.connected) {
                    document.getElementById('qrCodeImage').style.display = 'none';
                    document.getElementById('qrCodeMessage').style.display = 'none';
                    document.getElementById('regenerateForm').style.display = 'none';
                    document.getElementById('successMessage').style.display = 'block';
                    document.getElementById('disconnectForm').style.display = 'block';
                } else {
                    setTimeout(checkConnection, 5000); // Verifica novamente após 5 segundos
                }
            })
            .catch(error => {
                console.error('Erro ao verificar a conexão:', error);
                setTimeout(checkConnection, 5000); // Verifica novamente após 5 segundos
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        checkConnection();
    });
</script>

@endsection