@extends('panel.layout.settings')
@section('title', __('Clipdrop API Settings'))
@section('titlebar_actions', '')

@section('additional_css')
    <link
        href="{{ custom_theme_url('/assets/libs/select2/select2.min.css') }}"
        rel="stylesheet"
    />
@endsection

@section('settings')
    <form
        id="settings_form"
        onsubmit="return clipdropSettingsSave();"
        enctype="multipart/form-data"
    >
        <h3 class="mb-[25px] text-[20px]">{{ __('Clipdrop API Settings') }}</h3>
        <div class="row">
            <!-- TODO Serper api key -->
            @if ($app_is_demo)
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Clipdrop API Key') }}</label>
                        <input
                            class="form-control"
                            id="clipdrop_api_key"
                            type="text"
                            name="clipdrop_api_key"
                            value="*********************"
                        >
                    </div>
                </div>
            @else
                <div class="col-md-12">
                    <div
                        class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                        <label class="form-label">{{ __('Clipdrop API Key') }}</label>
                        <input
                            class="form-control"
                            id="clipdrop_api_key"
                            type="text"
                            name="clipdrop_api_key"
                            value="{{ setting('clipdrop_api_key')  }}"
                            required
                        >
                        <x-alert class="mt-2">
                            <p>
                                {{ __('Please ensure that your Clipdrop api key is fully functional and billing defined on your Clipdrop account.') }}
                            </p>
                        </x-alert>
{{--                        <a--}}
{{--                            href="https://serper.dev"--}}
{{--                            target="_blank"--}}
{{--                        >{{ __('Get Serper Api Key') }}</a>--}}

{{--                        <a--}}
{{--                            class="btn btn-primary mb-2 mt-2 w-full"--}}
{{--                            href="{{ route('dashboard.admin.settings.clipdrop.test') }}"--}}
{{--                            target="_blank"--}}
{{--                        >--}}
{{--                            {{ __('After Saving Setting, Click Here to Test Your api key') }}--}}
{{--                        </a>--}}
                    </div>
                </div>
            @endif

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
