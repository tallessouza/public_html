@extends('panel.layout.settings')
@section('title', __('Image Storage Settings'))
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
        onsubmit="return imageStorageSettingsSave();"
        enctype="multipart/form-data"
    >
        <h3 class="mb-[25px] text-[20px]">{{ __('Image Storage Settings') }}</h3>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Default Storage') }}</label>
                    <select
                        class="form-select"
                        id="ai_image_storage"
                        name="ai_image_storage"
                    >
                        <option
                            value="public"
                            {{ $settings_two->ai_image_storage == 'public' ? 'selected' : null }}
                        >
                            {{ __('Local Storage') }}</option>
                        <option
                            value="s3"
                            {{ $settings_two->ai_image_storage == 's3' ? 'selected' : null }}
                        >
                            {{ __('AWS S3') }}
                        </option>

                        @if (isset($cloudflare_r2) && $cloudflare_r2)
                            <option
                                value="r2"
                                {{ $settings_two->ai_image_storage == 'r2' ? 'selected' : null }}
                            >
                                {{ __('Cloudflare R2') }}
                            </option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-12">
                <button
                    class="btn btn-primary w-full"
                    id="settings_button"
                    form="settings_form"
                >
                    {{ __('Save') }}
                </button>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/select2/select2.min.js') }}"></script>
@endpush
