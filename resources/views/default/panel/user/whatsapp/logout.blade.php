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

@if ($serverError)
<x-card class:body="mt-4 flex flex-wrap justify-between" size="lg">
    <div class="mb-4 md:mb-0 lg:w-2/5">
        <h3 class="mb-7">{{ __('Erro no Servidor!') }}</h3>

        <p class="mb-3">
            Ocorreu um erro no servidor. Por favor, tente novamente mais tarde.
        </p>
    </div>
</x-card>
@elseif ($logoutSuccess)
<div class="py-10">
    <div class="container-xl">
        <div class="success-animation mb-3">
            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
            </svg>
        </div>
        <div class="text-center">
            <h1 class="text-heading text-center">{{ __('Desconexão bem-sucedida') }}</h1>
            <p class="text-heading my-3 text-center">
                {{ __('Seu número de WhatsApp foi desconectado com sucesso.') }}
            </p>
        </div>
        <div class="flex justify-center">
            <form method="GET" action="{{ route('dashboard.user.whatsapp') }}">
                @csrf
                <x-button class="mt-4 flex max-xl:hidden" type="submit">
                    <span class="max-lg:hidden">
                        {{ __('Reconectar') }}
                    </span>
                </x-button>
            </form>
        </div>
    </div>
</div>
@endif

@endsection