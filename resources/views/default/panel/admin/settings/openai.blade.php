@extends('panel.layout.settings', ['layout' => 'wide'])
@section('title', __('Openai Settings'))
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
        onsubmit="return openaiSettingsSave();"
        enctype="multipart/form-data"
    >
        <h3 class="mb-[25px] text-[20px]">{{ __('OpenAI Settings') }}</h3>
        <div class="row">
            <!-- TODO OPENAI API KEY -->
            @if ($app_is_demo)
                <div class="col-md-12">
                    <div class="mb-3">
                        <x-card
                            class="w-full"
                            size="sm"
                        >
                            <label class="form-label">{{ __('OpenAi API Secret') }}</label>
                            <input
                                class="form-control"
                                id="openai_api_secret"
                                type="text"
                                name="openai_api_secret"
                                value="*********************"
                            >
                        </x-card>
                    </div>
                </div>
            @else
                <div class="col-md-12">
                    <div class="mb-3">
                        <x-card
                            class="w-full"
                            size="sm"
                        >
                            <div
                                class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                                <label class="form-label">{{ __('OpenAi API Secret') }}</label>

                                <select
                                    class="form-control select2"
                                    id="openai_api_secret"
                                    name="openai_api_secret"
                                    multiple
                                >
                                    @foreach (explode(',', $setting->openai_api_secret) as $secret)
                                        <option
                                            value="{{ $secret }}"
                                            selected
                                        >{{ $secret }}</option>
                                    @endforeach
                                </select>

                                <x-alert class="mt-2">
                                    <p class="text-justify">
                                        {{ __('You can enter as much API KEY as you want. Click "Enter" after each api key.') }}
                                    </p>
                                </x-alert>
                                <x-alert class="mt-2">
                                    <p class="text-justify">
                                        {{ __('Please ensure that your OpenAI API key is fully functional and billing defined on your OpenAI account.') }}
                                    </p>
                                </x-alert>
                                <a
                                    class="btn btn-primary mb-2 mt-2 w-full"
                                    href="{{ route('dashboard.admin.settings.openai.test') }}"
                                    target="_blank"
                                >
                                    {{ __('After Saving Setting, Click Here to Test Your Api Keys') }}
                                </a>
                            </div>
                        </x-card>
                    </div>
                </div>
            @endif
            <div class="col-md-12">
                <div class="mb-3">
                    <x-card
                        class="w-full"
                        size="sm"
                    >
                        <label class="form-label">{{ __('Default Dall-E Model') }}</label>
                        <select
                            class="form-select"
                            id="dalle_default_model"
                            name="dalle_default_model"
                        >
                            <option
                                value="dalle2"
                                {{ $settings_two->dalle == 'dalle2' ? 'selected' : null }}
                            >
                                {{ __('Dall-E-2') }}</option>
                            <option
                                value="dalle3"
                                {{ $settings_two->dalle == 'dalle3' ? 'selected' : null }}
                            >
                                {{ __('Dall-E-3') }}</option>
                        </select>
                    </x-card>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <x-card
                        class="w-full"
                        size="sm"
                    >
                        <label class="form-label">{{ __('Default Openai Model') }}</label>
                        <select
                            class="form-select"
                            id="openai_default_model"
                            name="openai_default_model"
                        >
                            <!--
                                                                                                                                                                                                                                      <option value="text-ada-001" {{ $setting->openai_default_model == 'text-ada-001' ? 'selected' : null }}>{{ __('Ada (Cheapest &amp; Fastest)') }}</option>
                                                                                                                                                                                                                                      <option value="text-babbage-001" {{ $setting->openai_default_model == 'text-babbage-001' ? 'selected' : null }}>{{ __('Babbage') }}</option>
                                                                                                                                                                                                                                      <option value="text-curie-001" {{ $setting->openai_default_model == 'text-curie-001' ? 'selected' : null }}>{{ __('Curie') }}</option>
                                                                                                                                                                                                                                      -->
                            <option
                                value="davinci-002"
                                {{ $setting->openai_default_model == 'davinci-002' ? 'selected' : null }}
                            >
                                {{ __('Davinci (Expensive &amp; Capable)') }}</option>
                            <option
                                value="gpt-3.5-turbo-16k"
                                {{ $setting->openai_default_model == 'gpt-3.5-turbo-16k' ? 'selected' : null }}
                            >
                                {{ __('ChatGTP (3.5-turbo-16k)') }}</option>
                            <option
                                value="gpt-3.5-turbo"
                                {{ $setting->openai_default_model == 'gpt-3.5-turbo' ? 'selected' : null }}
                            >
                                {{ __('ChatGPT (Most Expensive & Fastest & Most Capable)') }}</option>

                            <option
                                value="gpt-3.5-turbo-0125"
                                {{ $setting->openai_default_model == 'gpt-3.5-turbo-0125' ? 'selected' : null }}
                            >
                                {{ __('ChatGTP (Updated Knowleddge cutoff of Sep 2021, 16k)') }}</option>
                            <option
                                value="gpt-4"
                                {{ $setting->openai_default_model == 'gpt-4' ? 'selected' : null }}
                            >
                                {{ __('ChatGPT-4 (Most Expensive & Fastest & Most Capable)') }}</option>

                            <option
                                value="gpt-4-1106-preview"
                                {{ $setting->openai_default_model == 'gpt-4-1106-preview' ? 'selected' : null }}
                            >
                                {{ __('GPT-4 Turbo (Updated Knowleddge cutoff of April 2023, 128k)') }}
                            </option>
                            <option
                                value="gpt-4-0125-preview"
                                {{ $setting->openai_default_model == 'gpt-4-0125-preview' ? 'selected' : null }}
                            >
                                {{ __('GPT-4 Turbo (Updated Knowleddge cutoff of Dec 2023, 128k)') }}
                            </option>
                            <option
                                value="gpt-4-turbo"
                                {{ $setting->openai_default_model == 'gpt-4-turbo' ? 'selected' : null }}
                            >
                                {{ __('GPT-4 Turbo with Vision (Updated Knowleddge cutoff of Dec 2023, 128k)') }}
                            </option>
                            <option
                                value="gpt-4o"
                                @selected($setting->openai_default_model == 'gpt-4o')
                            >
                                {{ __('GPT-4o Most advanced, multimodal flagship model that’s cheaper and faster than GPT-4 Turbo.  (Updated Knowleddge cutoff of Oct 2023, 128k)') }}
                            </option>
                            {{-- <option value="gpt-4-vision-preview" {{$setting->openai_default_model == 'gpt-4-vision-preview' ? 'selected' : null}}>{{__('GPT-4 Turbo with vision (Understand images, in addition to all other GPT-4 Turbo capabilites)')}}</option> --}}
                            @php App\Http\Controllers\AIFineTuneController::getFineModelOption( $setting->openai_default_model ); @endphp
                        </select>
                        <x-alert class="mt-2">
                            <p class="text-justify">
                                {{ __('Please note GPT-4 is not working with every api_key. You have to have an api key which can work with GPT-4.') }}
                            </p>
                            <p>
                                {{ __('Also please note that Chat models works with ChatGPT and GPT-4 models. So if you choose below it will automatically use ChatGPT.') }}
                            </p>
                        </x-alert>
                    </x-card>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <x-card
                        class="w-full"
                        size="sm"
                    >
                        <label class="form-label">{{ __('Default Stream Server') }}</label>
                        <select
                            class="form-select"
                            id="openai_default_stream_server"
                            type="text"
                            name="openai_default_stream_server"
                            required
                        >
                            <option
                                value="backend"
                                {{ $settings_two->openai_default_stream_server == 'backend' ? 'selected' : '' }}
                            >
                                {{ __('Backend') }}</option>
                            <option
                                value="frontend"
                                {{ $settings_two->openai_default_stream_server == 'frontend' ? 'selected' : '' }}
                            >
                                {{ __('Frontend') }}</option>
                        </select>
                    </x-card>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <x-card
                        class="w-full"
                        size="sm"
                    >
                        <label class="form-label">{{ __('Default Openai Language') }}</label>
                        <select
                            class="form-select"
                            id="openai_default_language"
                            name="openai_default_language"
                        >
                            @include('panel.admin.settings.languages')
                        </select>
                    </x-card>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <x-card
                        class="w-full"
                        size="sm"
                    >
                        <label class="form-label">{{ __('Default Tone of Voice') }}</label>
                        <select
                            class="form-select"
                            id="openai_default_tone_of_voice"
                            name="openai_default_tone_of_voice"
                        >
                            <option
                                value="Professional"
                                {{ $setting->openai_default_tone_of_voice == 'Professional' ? 'selected' : null }}
                            >
                                {{ __('Professional') }}</option>
                            <option
                                value="Funny"
                                {{ $setting->openai_default_tone_of_voice == 'Funny' ? 'selected' : null }}
                            >
                                {{ __('Funny') }}</option>
                            <option
                                value="Casual"
                                {{ $setting->openai_default_tone_of_voice == 'Casual' ? 'selected' : null }}
                            >
                                {{ __('Casual') }}</option>
                            <option
                                value="Excited"
                                {{ $setting->openai_default_tone_of_voice == 'Excited' ? 'selected' : null }}
                            >
                                {{ __('Excited') }}</option>
                            <option
                                value="Witty"
                                {{ $setting->openai_default_tone_of_voice == 'Witty' ? 'selected' : null }}
                            >
                                {{ __('Witty') }}</option>
                            <option
                                value="Sarcastic"
                                {{ $setting->openai_default_tone_of_voice == 'Sarcastic' ? 'selected' : null }}
                            >
                                {{ __('Sarcastic') }}</option>
                            <option
                                value="Feminine"
                                {{ $setting->openai_default_tone_of_voice == 'Feminine' ? 'selected' : null }}
                            >
                                {{ __('Feminine') }}</option>
                            <option
                                value="Masculine"
                                {{ $setting->openai_default_tone_of_voice == 'Masculine' ? 'selected' : null }}
                            >
                                {{ __('Masculine') }}</option>
                            <option
                                value="Bold"
                                {{ $setting->openai_default_tone_of_voice == 'Bold' ? 'selected' : null }}
                            >
                                {{ __('Bold') }}</option>
                            <option
                                value="Dramatic"
                                {{ $setting->openai_default_tone_of_voice == 'Dramatic' ? 'selected' : null }}
                            >
                                {{ __('Dramatic') }}</option>
                            <option
                                value="Grumpy"
                                {{ $setting->openai_default_tone_of_voice == 'Grumpy' ? 'selected' : null }}
                            >
                                {{ __('Grumpy') }}</option>
                            <option
                                value="Secretive"
                                {{ $setting->openai_default_tone_of_voice == 'Secretive' ? 'selected' : null }}
                            >
                                {{ __('Secretive') }}</option>
                        </select>

                        <x-forms.input
                            class:container="mt-5"
                            id="hide_tone_of_voice_option"
                            type="checkbox"
                            switcher
                            type="checkbox"
                            :checked="setting('hide_tone_of_voice_option') == 1"
                            label="{{ __('Hide Tone of Voice Option') }}"
                            tooltip="{{ __('If this is enabled users will not see the tone of voice option in generator options.') }}"
                        />
                    </x-card>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <x-card
                        class="w-full"
                        size="sm"
                    >
                        <label class="form-label">{{ __('Default Creativity') }}</label>
                        <select
                            class="form-select"
                            id="openai_default_creativity"
                            type="text"
                            name="openai_default_creativity"
                            required
                        >
                            <option
                                value="0.25"
                                {{ $setting->openai_default_creativity == 0.25 ? 'selected' : '' }}
                            >
                                {{ __('Economic') }}</option>
                            <option
                                value="0.5"
                                {{ $setting->openai_default_creativity == 0.5 ? 'selected' : '' }}
                            >
                                {{ __('Average') }}</option>
                            <option
                                value="0.75"
                                {{ $setting->openai_default_creativity == 0.75 ? 'selected' : '' }}
                            >
                                {{ __('Good') }}</option>
                            <option
                                value="1"
                                {{ $setting->openai_default_creativity == 1 ? 'selected' : '' }}
                            >
                                {{ __('Premium') }}</option>
                        </select>
                        <x-forms.input
                            class:container="mt-5"
                            id="hide_creativity_option"
                            type="checkbox"
                            switcher
                            type="checkbox"
                            :checked="setting('hide_creativity_option') == 1"
                            label="{{ __('Hide Creativity Option') }}"
                            tooltip="{{ __('If this is enabled users will not see the creativity option in generator options.') }}"
                        />
                    </x-card>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <x-card
                        class="w-full"
                        size="sm"
                    >
                        <label class="form-label">{{ __('Maximum Output Length') }}</label>
                        <input
                            class="form-control"
                            id="openai_max_output_length"
                            type="number"
                            name="openai_max_output_length"
                            min="0"
                            value="{{ $setting->openai_max_output_length }}"
                            required
                        >
                        <x-forms.input
                            class:container="mt-5 mb-3"
                            id="hide_output_length_option"
                            type="checkbox"
                            switcher
                            type="checkbox"
                            :checked="setting('hide_output_length_option') == 1"
                            label="{{ __('Hide Output Length Option') }}"
                            tooltip="{{ __('If this is enabled users will not see the output length option in generator options.') }}"
                        />
                        <x-alert class="mt-2">
                            <p class="text-justify">
                                {{ __('In Words. OpenAI has a hard limit based on Token limits for each model. Refer to OpenAI documentation to learn more. As a recommended by OpenAI, max result length is capped at 2000 tokens') }}
                            </p>
                            <p class="text-justify">
                                {{ __('The maximum output length refers to the point at which the AI-generated response will stop. It can occur when the response reaches 4096 bytes or when the generated content is considered sufficient for the given context.') }}
                            </p>
                        </x-alert>
                    </x-card>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <x-card
                        class="w-full"
                        size="sm"
                    >
                        <label class="form-label">{{ __('Maximum Input Length') }}</label>
                        <input
                            class="form-control"
                            id="openai_max_input_length"
                            type="number"
                            name="openai_max_input_length"
                            min="10"
                            max="2000"
                            value="{{ $setting->openai_max_input_length }}"
                            required
                        >
                        <x-alert class="mt-2">
                            <p class="text-justify">
                                {{ __('In Characters') }}
                            </p>
                        </x-alert>
                    </x-card>
                </div>
            </div>
        </div>

        <h3 class="mb-[25px] mt-5 text-[20px]">{{ __('Fine Tune') }}</h3>
        <div class="row">
            <div class="mb-4">
                <button
                    class="btn btn-default"
                    data-bs-toggle="modal"
                    data-bs-target="#addFineTuneModel"
                    type="button"
                >
                    <svg
                        class="mr-2"
                        xmlns="http://www.w3.org/2000/svg"
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path
                            stroke="none"
                            d="M0 0h24v24H0z"
                            fill="none"
                        ></path>
                        <path d="M12 5l0 14"></path>
                        <path d="M5 12l14 0"></path>
                    </svg>
                    {{ __('Add Fine Tune') }}
                </button>

            </div>
            <div class="table-responsive fine-tune-table">
                <table class="table-vcenter table">
                    <thead>
                        <tr>
                            <th>{{ __('Custom Name') }}</th>
                            <th>{{ __('File ID') }}</th>
                            <th>{{ __('Bytes') }}</th>
                            <th>{{ __('Base Model') }}</th>
                            <th>{{ __('Fine Tuned Model') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php App\Http\Controllers\AIFineTuneController::getFineTuneTableRow(); @endphp
                    </tbody>
                </table>
            </div>
        </div>
        <button
            class="btn btn-primary w-full"
            id="settings_button"
            form="settings_form"
            onclick="checkMaxOutputLength()"
        >
            {{ __('Save') }}
        </button>
    </form>

    <div
        class="modal"
        id="addFineTuneModel"
        tabindex="-1"
    >
        <div
            class="modal-dialog modal-lg"
            role="document"
        >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add Fine Tune') }}</h5>
                    <button
                        class="btn-close"
                        data-bs-dismiss="modal"
                        type="button"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3">
                            <label
                                class="form-label"
                                for="fine_tune_name"
                            >
                                {{ __('Name') }}
                            </label>
                            <input
                                class="form-control"
                                id="fine_tune_name"
                                type="text"
                                name="fine_tune_name"
                                placeholder="{{ __('Enter name') }}"
                                required
                            >
                        </div>
                        <div class="mb-3">
                            <label
                                class="form-label"
                                for="fine_tune_model"
                            >
                                {{ __('Model') }}
                            </label>
                            <select
                                class="form-select"
                                id="fine_tune_model"
                                name="fine_tune_model"
                            >
                                <option value="gpt-3.5-turbo-1106">gpt-3.5-turbo-1106</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label
                                class="form-label"
                                for="fine_tune_purpose"
                            >
                                {{ __('Purpose') }}
                            </label>
                            <select
                                class="form-select"
                                id="fine_tune_purpose"
                                name="fine_tune_purpose"
                            >
                                <option value="fine-tune">{{ __('Fine Tune') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label
                                class="form-label"
                                for="fine_tune_file"
                            >
                                {{ __('Select File (JSON)') }}
                            </label>
                            <input
                                class="form-control"
                                id="fine_tune_file"
                                type="file"
                                name="fine_tune_file"
                                accept=".jsonl"
                                required
                            >
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        class="btn btn-primary add-fine-tune"
                        data-bs-dismiss="modal"
                        type="button"
                    >
                        {{ __('Add') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function checkMaxOutputLength() {
            var maxOutputLength = document.getElementById("openai_max_output_length").value;
            var msg = "{{ __('The maximum output length is set above 2000. Are you sure you want to continue?') }}";
            if (maxOutputLength > 2000) {
                var confirmation = confirm(msg);
                if (!confirmation) {
                    event.preventDefault();
                }
            }
        }
    </script>
    <script>
        $(document).on("click", ".add-fine-tune", function(e) {
            "use strict";

            var formData = new FormData();
            formData.append('title', $('#fine_tune_name').val());
            formData.append('model', $('#fine_tune_model').val());
            formData.append('purpose', $('#fine_tune_purpose').val());

            if ($('#file').val() != 'undefined') {
                formData.append('file', $('#fine_tune_file').prop('files')[0]);
            }

            $.ajax({
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                url: "/dashboard/user/openai/add-fine-tune",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    // $('.fetch-rss svg').addClass('animate-spin');
                },
                success: function(data) {
                    // $('.fetch-rss svg').removeClass('animate-spin');
                    if (data.output) {
                        $('.fine-tune-table tbody').prepend(data.output);
                        toastr.success(@json(__('Fine Tune Created!')));
                        $('#fine_tune_name').val('');
                        $('#fine_tune_model').val('');
                        $('#fine_tune_purpose').val('');
                        $('#fine_tune_file').val('');
                    }
                },
                error: function(data) {
                    // $('.fetch-rss svg').removeClass('animate-spin');
                    toastr.error(data.responseJSON);
                }
            });

        });
    </script>
    <script>
        $(document).on("click", ".delete-fine-tune", function(e) {
            "use strict";

            let button = $(this);
            let file_id = button.attr('data-file');
            let model = button.attr('data-model');
            let row = button.closest('tr');

            if (!confirm(@json(__('Are you sure?')))) {
                return false;
            }

            var formData = new FormData();
            formData.append('file_id', file_id);
            formData.append('model', model);

            if (!file_id || !model) {
                toastr.error(@json(__('Model under on process. Reload the page before delete!')));
                return false;
            }

            $.ajax({
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                url: "/dashboard/user/openai/delete-fine-tune",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    // $('.fetch-rss svg').addClass('animate-spin');
                },
                success: function(data) {
                    row.remove();
                    toastr.success(@json(__('Fine Tune Deleted!')));
                },
                error: function(data) {
                    toastr.error(data.responseJSON);
                }
            });

        });
    </script>
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/select2/select2.min.js') }}"></script>
@endpush
