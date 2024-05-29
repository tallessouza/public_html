@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('AI Article Wizard'))
@section('titlebar_actions', '')
@section('titlebar_subtitle', __('Just choose your topic, and watch AI whip up SEO-optimized blog content in a matter of seconds!'))
@section('titlebar_actions_before')
    <div class="mb-4 flex w-full justify-end">
        <x-remaining-credit
            class="text-2xs"
            legend-size="sm"
            style="inline"
            progress-height="sm"
        />
    </div>
@endsection

@section('content')
    <div class="py-10">
        @include('panel.user.article_wizard.components.wizard_settings')
    </div>
    <input
        id="guest_id"
        type="hidden"
        value="{{ $apiUrl }}"
    >
    <input
        id="guest_event_id"
        type="hidden"
        value="{{ $apikeyPart1 }}"
    >
    <input
        id="guest_look_id"
        type="hidden"
        value="{{ $apikeyPart2 }}"
    >
    <input
        id="guest_product_id"
        type="hidden"
        value="{{ $apikeyPart3 }}"
    >
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script>
        let stream_type = '{!! $settings_two->openai_default_stream_server !!}';
        const openai_model = '{{ $setting->openai_default_model }}';
        const guest_id = document.getElementById("guest_id")?.value;
        const guest_event_id = document.getElementById("guest_event_id")?.value;
        const guest_look_id = document.getElementById("guest_look_id")?.value;
        const guest_product_id = document.getElementById("guest_product_id")?.value;
    </script>
    <script src="{{ custom_theme_url('/assets/js/panel/article_wizard.js') }}"></script>
    <script>
        let selected_step = -1;
        @if (isset($wizard))
            CUR_STATE = {
                ...@json($wizard)
            };
            selected_step = CUR_STATE.current_step;
            image_storage = @json($settings_two->ai_image_storage);
            updateData();
        @endif
    </script>
@endpush
