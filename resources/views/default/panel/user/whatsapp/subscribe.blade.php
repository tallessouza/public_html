@extends('panel.layout.app', ['disable_tblr' => true])

@section('title', __('WhatsApp'))

@section('titlebar_title')
    {{ __('WhatsApp') }}
@endsection

@section('additional_css')
    <style>
        .xmark {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: block;
            stroke-width: 2;
            stroke: #e3342f;
            stroke-miterlimit: 10;
            box-shadow: inset 0px 0px 0px #e3342f;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
            position: relative;
            top: 5px;
            right: 5px;
            margin: 0 auto;
        }

        .xmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke-miterlimit: 10;
            stroke: #e3342f;
            fill: transparent;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }

        .xmark__x {
            transform-origin: 50% 50%;
            stroke-dasharray: 80;
            stroke-dashoffset: 80;
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
                box-shadow: inset 0px 0px 0px 4px #e3342f;
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
@else
    <div class="py-10">
        <div class="container-xl">
            <div class="error-animation mb-3">
                <svg class="xmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="xmark__circle" cx="26" cy="26" r="25" fill="none" />
                    <path class="xmark__x" fill="none" d="M16 16 L36 36 M36 16 L16 36" />
                </svg>
            </div>
            <div class="text-center">
                <h1 class="text-heading text-center">{{ __('Sem assinatura') }}</h1>
                <p class="text-heading my-3 text-center">
                    {{ __('Essa funcionalidade é exclusiva para membros assinantes.') }}
                </p>
            </div>
            <div class="flex justify-center">
                <form method="GET" action="{{ route('dashboard.user.payment.subscription') }}">
                    @csrf
                    <x-button class="mt-4 flex max-xl:hidden" type="submit">
                        <span class="max-lg:hidden">
                            {{ __('Subscribe') }}
                        </span>
                    </x-button>
                </form>
            </div>
        </div>
    </div>
@endif

@endsection
