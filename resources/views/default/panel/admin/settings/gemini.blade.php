@extends('panel.layout.settings', ['layout' => 'wide'])
@section('title', __('Gemini Settings'))
@section('titlebar_actions', '')
@section('additional_css')
    <link
        href="{{ custom_theme_url('/assets/libs/select2/select2.min.css') }}"
        rel="stylesheet"
    />
    <style>

    </style>
@endsection

@section('settings')
    <form
        id="settings_form"
        onsubmit="return geminiSettingsSave();"
        enctype="multipart/form-data"
    >
        <div class="row">
            <x-card
                class="mb-3 max-md:text-center"
                szie="lg"
            >
                @if ($app_is_demo)
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Gemini API Secret') }}</label>
                            <input
                                class="form-control"
                                id="gemini_api_secret"
                                type="text"
                                name="gemini_api_secret"
                                value="*********************"
                            >
                        </div>
                    </div>
                @else
                    <div class="col-md-12">
                        <div
                            class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                            <label class="form-label">{{ __('Gemini API Secret') }}
                                <x-alert class="mt-2">
                                    <x-button
                                        variant="link"
                                        href="https://makersuite.google.com/app/apikey"
                                        target="_blank"
                                    >
                                        {{ __('Get an API key') }}
                                    </x-button>
                                </x-alert>
                            </label>

                            <select
                                class="form-control select2"
                                id="gemini_api_secret"
                                name="gemini_api_secret"
                                multiple
                            >
                                @foreach (explode(',', setting('gemini_api_secret')) as $secret)
                                    <option
                                        value="{{ $secret }}"
                                        selected
                                    >{{ $secret }}</option>
                                @endforeach
                            </select>

                            <x-alert class="mt-2">
                                <p>
                                    {{ __('You can enter as much API KEY as you want. Click "Enter" after each api key.') }}
                                </p>
                            </x-alert>
                            <x-alert class="mt-2">
                                <p>
                                    {{ __('Please ensure that your Gemini API key is fully functional and billing defined on your Gemini account.') }}
                                </p>
                            </x-alert>
                            <a
                                class="btn btn-primary mb-2 mt-2 w-full"
                                href="{{ route('dashboard.admin.settings.gemini.test') }}"
                                target="_blank"
                            >
                                {{ __('After Saving Setting, Click Here to Test Your Api Keys') }}
                            </a>
                        </div>
                    </div>
                @endif
            </x-card>
            <x-card
                class="mb-3 max-md:text-center"
                szie="lg"
            >
                <div class="mb-3">
                    <label class="form-label">{{ __('Default Gemini Model') }}
                        <x-alert class="mt-2">
                            <x-button
                                variant="link"
                                href="https://ai.google.dev/gemini-api/docs/models/gemini#model-variations"
                                target="_blank"
                            >
                                {{ __('For more info about models') }}
                            </x-button>
                        </x-alert>
                    </label>
                    <select
                        class="form-select"
                        id="gemini_default_model"
                        name="gemini_default_model"
                    >
                        <option
                            value="gemini-1.5-pro-latest"
                            {{ setting('gemini_default_model', 'gemini-pro') == 'gemini-1.5-pro-latest' ? 'selected' : null }}
                        >
                            {{ __('Gemini 1.5 Pro (Preview only) (Model last updated: April 2024)') }}
                        </option>
                        <option
                            value="gemini-pro"
                            {{ setting('gemini_default_model', 'gemini-pro') == 'gemini-pro' ? 'selected' : null }}
                        >
                            {{ __('Gemini 1.0 Pro (Model last updated: February 2024)') }}
                        </option>
                        <option
                            value="gemini-pro-vision"
                            {{ setting('gemini_default_model', 'gemini-pro') == 'gemini-pro-vision' ? 'selected' : null }}
                        >
                            {{ __('Gemini 1.0 Pro Vision (Model last updated: February 2023)') }}
                        </option>
{{--                        <option--}}
{{--                            value="aqa"--}}
{{--                            {{ setting('gemini_default_model', 'gemini-pro') == 'aqa' ? 'selected' : null }}--}}
{{--                        >--}}
{{--                            {{ __('AQA (Model last updated: December 2023)') }}--}}
{{--                        </option>--}}
                    </select>
                </div>

                <div class="mb-3">
                    <x-card
                        class="w-full"
                        size="sm"
                    >
                        <label class="form-label">{{ __('Maximum Input Length') }}</label>
                        <input
                            class="form-control"
                            id="gemini_max_input_length"
                            type="number"
                            name="gemini_max_input_length"
                            min="0"
                            value="{{ setting('gemini_max_input_length', 2000) }}"
                            required
                        >
                        <x-alert class="mt-2">
                            <p class="text-justify">
                                {{ __('In Characters') }}
                            </p>
                        </x-alert>
                    </x-card>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Maximum Output Length') }}</label>
                    <input
                        class="form-control"
                        id="gemini_max_output_length"
                        type="number"
                        name="gemini_max_output_length"
                        min="0"
                        value="{{ setting('gemini_max_output_length', 400) }}"
                        required
                    >
                </div>

            </x-card>
        </div>

        <button
            class="btn btn-primary w-full"
            id="settings_button"
            form="settings_form"
        >
            {{ __('Save') }}
        </button>
    </form>

@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/select2/select2.min.js') }}"></script>
@endpush
