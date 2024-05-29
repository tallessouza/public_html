@extends('panel.layout.settings')
@section('title', __('General Settings'))
@section('titlebar_actions', '')
@section('additional_css')
    <link
        rel="stylesheet"
        href="https://foliotek.github.io/Croppie/croppie.css"
    />
    <style>
        #upload-demo {
            width: 250px;
            height: 250px;
            padding-bottom: 25px;
            margin: 0 auto;
        }
    </style>
@endsection

@section('settings')
    <form
        id="settings_form"
        onsubmit="return generalSettingsSave();"
        enctype="multipart/form-data"
    >
        <h3 class="mb-[25px] text-[20px]">{{ __('Global Settings') }}</h3>
        <div class="row mb-4">

            <x-forms.input
                class:container="mb-5"
                id="login_without_confirmation"
                type="checkbox"
                switcher
                type="checkbox"
                :checked="$setting->login_without_confirmation == 0"
                label="{{ __('Disable Login Without Confirmation') }}"
                tooltip="{{ __('If this is enabled users cannot login unless they confirm their emails.') }}"
            />

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Site Name') }}</label>
                    <input
                        class="form-control"
                        id="site_name"
                        type="text"
                        name="site_name"
                        value="{{ $setting->site_name }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Site URL') }}</label>
                    <input
                        class="form-control"
                        id="site_url"
                        type="text"
                        name="site_url"
                        value="{{ $setting->site_url }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Site Email') }}</label>
                    <input
                        class="form-control"
                        id="site_email"
                        type="text"
                        name="site_email"
                        value="{{ $setting->site_email }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Default Country') }}</label>
                    <select
                        class="form-select"
                        id="default_country"
                        name="default_country"
                    >
                        @include('panel.admin.settings.countries')
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Default Currency') }}</label>
                    <select
                        class="form-select"
                        id="default_currency"
                        name="default_currency"
                    >
                        @include('panel.admin.settings.currencies')
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Registration Active') }}</label>
                    <select
                        class="form-select"
                        id="register_active"
                        name="register_active"
                    >
                        <option
                            value="1"
                            {{ $setting->register_active == 1 ? 'selected' : '' }}
                        >
                            {{ __('Active') }}</option>
                        <option
                            value="0"
                            {{ $setting->register_active == 0 ? 'selected' : '' }}
                        >
                            {{ __('Passive') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Default ai engine') }}
                        <x-badge
                            class="ms-2 text-2xs"
                            variant="secondary"
                        >
                            @lang('New')
                        </x-badge>
                    </label>
                    <select
                        class="form-select"
                        id="default_ai_engine"
                        name="default_ai_engine"
                    >
                        <option
                            value="openai"
                            {{ setting('default_ai_engine') == 'openai' ? 'selected' : '' }}
                        >
                            {{ __('Openai') }}</option>
                        <option
                            value="anthropic"
                            {{ setting('default_ai_engine') == 'anthropic' ? 'selected' : '' }}
                        >
                            {{ __('Anthropic') }}</option>
                        <option
                            value="gemini"
                            {{ setting('default_ai_engine') == 'gemini' ? 'selected' : '' }}
                        >
                            {{ __('Gemini') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Article Wizard default image engine') }}
                        <x-badge
                            class="ms-2 text-2xs"
                            variant="secondary"
                        >
                            @lang('New')
                        </x-badge>
                    </label>
                    <select
                        class="form-select"
                        id="default_aw_image_engine"
                        name="default_aw_image_engine"
                    >
                        <option
                            value="unsplash"
                            {{ setting('default_aw_image_engine', 'unsplash') == 'unsplash' ? 'selected' : '' }}
                        >
                            {{ __('Unsplash') }}</option>
                        <option
                            value="pexels"
                            {{ setting('default_aw_image_engine', 'unsplash') == 'unsplash' ? 'selected' : '' }}
                        >
                            {{ __('Pexels') }}</option>
                        <option
                            value="pixabay"
                            {{ setting('default_aw_image_engine', 'unsplash') == 'unsplash' ? 'selected' : '' }}
                        >
                            {{ __('Pixabay') }}</option>
                        <option
                            value="openai"
                            {{ setting('default_aw_image_engine', 'unsplash') == 'openai' ? 'selected' : '' }}
                        >
                            {{ __('Openai Dall-E') }}</option>
                        <option
                            value="sd"
                            {{ setting('default_aw_image_engine', 'unsplash') == 'sd' ? 'selected' : '' }}
                        >
                            {{ __('Stable Diffusion') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label">{{ __('Free Usage Upon Registration (words,images)') }}</label>
                <input
                    class="form-control"
                    id="free_plan"
                    type="text"
                    name="free_plan"
                    value="{{ $setting->free_plan }}"
                >
            </div>
        </div>

        <x-forms.input
            class:container="mb-2"
            id="limit"
            type="checkbox"
            name="limit"
            :checked="$settings_two?->daily_limit_enabled == 1"
            label="{{ __('Apply daily limit on image generation') }}"
            switcher
        />

        <div
            class="mb-[20px]"
            id="countField"
            style="{{ $settings_two?->daily_limit_enabled == 1 ? '' : 'display:none' }}"
        >
            <label class="form-label">{{ __('Daily Image Limit Count') }}</label>
            <input
                class="form-control"
                id="daily_limit_count"
                type="text"
                name="daily_limit_count"
                value="{{ $settings_two?->allowed_images_count }}"
            >
        </div>

        <div class="mb-[20px]">
            <x-forms.input
                class:container="mb-2"
                id="voice_limit"
                type="checkbox"
                name="voice_limit"
                :checked="$settings_two?->daily_voice_limit_enabled == 1"
                label="{{ __('Apply daily limit on voice generation') }}"
                switcher
            />
        </div>

        <div
            class="mb-[20px]"
            id="voiceCountField"
            style="{{ $settings_two?->daily_voice_limit_enabled == 1 ? '' : 'display:none' }}"
        >
            <label class="form-label">{{ __('Daily Voice Limit Count') }}</label>
            <input
                class="form-control"
                id="daily_voice_limit_count"
                type="text"
                name="daily_voice_limit_count"
                value="{{ $settings_two?->allowed_voice_count }}"
            >
        </div>

        <h3 class="mb-[25px] mt-7 text-[20px]">{{ __('Social Login') }}</h3>
        <div class="row mb-4">
            <div class="mb-3">
                <x-alert class="rounde mb-4">
                    <a
                        href="https://magicaidocs.liquid-themes.com/social-login"
                        target="_blank"
                    >
                        {{ __('Check the documentation.') }}
                        <x-tabler-arrow-up-right class="size-4 inline align-text-bottom" />
                    </a>
                </x-alert>

                <x-forms.input
                    class:container="mb-2"
                    id="facebook_active"
                    type="checkbox"
                    :checked="$setting->facebook_active == 1"
                    switcher
                    label="{{ __('Facebook') }}"
                />
                <x-forms.input
                    class:container="mb-2"
                    id="google_active"
                    type="checkbox"
                    :checked="$setting->google_active == 1"
                    switcher
                    label="{{ __('Google') }}"
                />
                <x-forms.input
                    class:container="mb-2"
                    id="github_active"
                    type="checkbox"
                    :checked="$setting->github_active == 1"
                    switcher
                    label="{{ __('Github') }}"
                />
            </div>
        </div>

        <h3 class="mb-[25px] mt-7 text-[20px]">{{ __('Logo Settings') }}</h3>
        <div class="row mb-4">
            <div class="col-md-12 mb-3">
                <div class="mb-4">
                    <label class="form-label">{{ __('Site Favicon') }}</label>
                    <input
                        class="form-control"
                        id="favicon"
                        type="file"
                        name="favicon"
                    >
                </div>
                <x-alert class="!mt-2">
                    <p>
                        {{ __('If you will use SVG, you do not need the Retina (2x) option.') }}
                    </p>
                </x-alert>
            </div>

            <div class="col-md-6">
                <h4 class="mb-3">{{ __('Default Logos') }}</h4>

                <div class="mb-3">
                    <label class="form-label">{{ __('Site Logo') }}</label>
                    <input
                        class="form-control item-img"
                        id="logo"
                        data-id="logo"
                        type="file"
                        name="logo"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Site Logo (Dark)') }}</label>
                    <input
                        class="form-control item-img"
                        id="logo_dark"
                        data-id="logo_dark"
                        type="file"
                        name="logo_dark"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Site Logo Sticky') }}</label>
                    <input
                        class="form-control item-img"
                        id="logo_sticky"
                        data-id="logo_sticky"
                        type="file"
                        name="logo_sticky"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo') }}</label>
                    <input
                        class="form-control item-img"
                        id="logo_dashboard"
                        data-id="logo_dashboard"
                        type="file"
                        name="logo_dashboard"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo (Dark)') }}</label>
                    <input
                        class="form-control item-img"
                        id="logo_dashboard_dark"
                        data-id="logo_dashboard_dark"
                        type="file"
                        name="logo_dashboard_dark"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo Collapsed') }}</label>
                    <input
                        class="form-control item-img"
                        id="logo_collapsed"
                        data-id="logo_collapsed"
                        type="file"
                        name="logo_collapsed"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo Collapsed (Dark)') }}</label>
                    <input
                        class="form-control item-img"
                        id="logo_collapsed_dark"
                        data-id="logo_collapsed_dark"
                        type="file"
                        name="logo_collapsed_dark"
                    >
                </div>

            </div>
            <div class="col-md-6">
                <h4 class="mb-3">{{ __('Retina Logos (2x) - Optional') }}</h4>

                <div class="mb-3">
                    <label class="form-label">{{ __('Site Logo') }}</label>
                    <input
                        class="form-control item-img-x2"
                        id="logo_2x"
                        data-id="logo_2x"
                        type="file"
                        name="logo_2x"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Site Logo (Dark)') }}</label>
                    <input
                        class="form-control item-img-x2"
                        id="logo_dark_2x"
                        data-id="logo_dark_2x"
                        type="file"
                        name="logo_dark_2x"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Site Logo Sticky') }}</label>
                    <input
                        class="form-control item-img-x2"
                        id="logo_sticky_2x"
                        data-id="logo_sticky_2x"
                        type="file"
                        name="logo_sticky_2x"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo') }}</label>
                    <input
                        class="form-control item-img-x2"
                        id="logo_dashboard_2x"
                        data-id="logo_dashboard_2x"
                        type="file"
                        name="logo_dashboard_2x"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo (Dark)') }}</label>
                    <input
                        class="form-control item-img-x2"
                        id="logo_dashboard_dark_2x"
                        data-id="logo_dashboard_dark_2x"
                        type="file"
                        name="logo_dashboard_dark_2x"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo Collapsed') }}</label>
                    <input
                        class="form-control item-img-x2"
                        id="logo_collapsed_2x"
                        data-id="logo_collapsed_2x"
                        type="file"
                        name="logo_collapsed_2x"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo Collapsed (Dark)') }}</label>
                    <input
                        class="form-control item-img-x2"
                        id="logo_collapsed_dark_2x"
                        data-id="logo_collapsed_dark_2x"
                        type="file"
                        name="logo_collapsed_dark_2x"
                    >
                </div>
            </div>
        </div>

        <h3 class="mb-[25px] mt-7 text-[20px]">{{ __('Seo Settings') }}</h3>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="mb-4">
                    <label class="form-label">{{ __('Google Analytics Tracking ID') }} (UA-1xxxxx)
                        {{ __('or') }} (G-xxxxxx)</label>
                    <input
                        class="form-control"
                        id="google_analytics_code"
                        type="text"
                        name="google_analytics_code"
                        value="{{ $setting->google_analytics_code }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label m-0">{{ __('Meta Title') }}</label>
                        <select
                            class="form-control min-w-36 m-0 bg-[#F1EDFF] py-1"
                            id="metaTitleLocal"
                            style="width: auto;"
                            name="metaTitleLocal"
                            onchange="handleSelectChangeLang('meta_title');"
                        >
                            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                @if (in_array($localeCode, explode(',', $settings_two->languages)))
                                    <option
                                        class="p-0"
                                        value="{{ $localeCode }}"
                                        @if ($settings_two->languages_default === $localeCode) {{ 'selected' }} @endif
                                    >
                                        <span class="!me-2 text-[21px]">{{ country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1)) }}</span>
                                        {{ ucfirst($properties['native']) }} @if ($settings_two->languages_default === $localeCode)
                                        @endif
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <input
                        class="form-control {{ setting('serper_seo_site_meta', 0) == 1 ? 'input-seo' : '' }}"
                        id="meta_title"
                        type="text"
                        name="meta_title"
                        value="{{ $setting->meta_title }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label m-0">{{ __('Meta Description') }}</label>
                        <select
                            class="form-control min-w-36 m-0 bg-[#F1EDFF] py-1"
                            id="metaDescLocal"
                            style="width: auto;"
                            name="metaDescLocal"
                            onchange="handleSelectChangeLang('meta_desc');"
                        >
                            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                @if (in_array($localeCode, explode(',', $settings_two->languages)))
                                    <option
                                        class="p-0"
                                        value="{{ $localeCode }}"
                                        @if ($settings_two->languages_default === $localeCode) {{ 'selected' }} @endif
                                    >
                                        <span class="!me-2 text-[21px]">{{ country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1)) }}</span>
                                        {{ ucfirst($properties['native']) }} @if ($settings_two->languages_default === $localeCode)
                                        @endif
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <textarea
                        class="form-control {{ setting('serper_seo_site_meta', 0) == 1 ? 'input-seo' : '' }}"
                        id="meta_description"
                        name="meta_description"
                        rows="5"
                    >{{ $setting->meta_description }}</textarea>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Meta Keywords') }}</label>
                    <textarea
                        class="form-control {{ setting('serper_seo_site_meta', 0) == 1 ? 'input-seo' : '' }}"
                        id="meta_keywords"
                        name="meta_keywords"
                        placeholder="{{ __('ChatGPT, AI Writer, AI Image Generator, AI Chat') }}"
                        rows="3"
                    >{{ $setting->meta_keywords }}</textarea>
                </div>
            </div>
        </div>

        <h3 class="mb-[25px] mt-7 text-[20px]">{{ __('Advanced Settings') }}</h3>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('Code before </head> (Dashboard)') }}
                        <x-info-tooltip text="{{ __('Only accepts javascript code wrapped with <script> tags and HTML markup that is valid inside the </head> tag.') }}" />
                    </label>
                    <textarea
                        class="form-control"
                        id="dashboard_code_before_head"
                        name="dashboard_code_before_head"
                    >{{ $setting->dashboard_code_before_head }}</textarea>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('Code before </body> (Dashboard)') }}
                        <x-info-tooltip text="{{ __('Only accepts javascript code wrapped with <script> tags and HTML markup that is valid inside the </body> tag.') }}" />
                    </label>
                    <textarea
                        class="form-control"
                        id="dashboard_code_before_body"
                        name="dashboard_code_before_body"
                    >{{ $setting->dashboard_code_before_body }}</textarea>
                </div>
            </div>
        </div>

        <h3 class="mb-[25px] mt-7 text-[20px]">{{ __('Manage the Features') }}</h3>
        <div class="row mb-4">
            <div class="mb-3">
                <div class="form-label">{{ __('Manage the features you want to activate for users.') }}</div>
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_writer"
                    type="checkbox"
                    :checked="$setting->feature_ai_writer == 1"
                    label="{{ __('AI Writer') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_advanced_editor"
                    type="checkbox"
                    :checked="$setting->feature_ai_advanced_editor == 1"
                    label="{{ __('AI advanced editor') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_image"
                    type="checkbox"
                    :checked="$setting->feature_ai_image == 1"
                    label="{{ __('AI Image') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_video"
                    type="checkbox"
                    :checked="$settings_two->feature_ai_video == 1"
                    label="{{ __('AI Video') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_chat"
                    type="checkbox"
                    :checked="$setting->feature_ai_chat == 1"
                    label="{{ __('AI Chat') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_code"
                    type="checkbox"
                    :checked="$setting->feature_ai_code == 1"
                    label="{{ __('AI Code') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_speech_to_text"
                    type="checkbox"
                    :checked="$setting->feature_ai_speech_to_text == 1"
                    label="{{ __('AI Speech to Text') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_voiceover"
                    type="checkbox"
                    :checked="$setting->feature_ai_voiceover == 1"
                    label="{{ __('AI Voiceover') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_affilates"
                    type="checkbox"
                    :checked="$setting->feature_affilates == 1"
                    label="{{ __('Affilates') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_article_wizard"
                    type="checkbox"
                    :checked="$setting->feature_ai_article_wizard == 1"
                    label="{{ __('Article Wizard') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_vision"
                    type="checkbox"
                    :checked="$setting->feature_ai_vision == 1"
                    label="{{ __('AI Vision') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_chat_image"
                    type="checkbox"
                    :checked="$setting->feature_ai_chat_image == 1"
                    label="{{ __('Chat Image') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_pdf"
                    type="checkbox"
                    :checked="$setting->feature_ai_pdf == 1"
                    label="{{ __('AI File Chat') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_rewriter"
                    type="checkbox"
                    :checked="$setting->feature_ai_rewriter == 1"
                    label="{{ __('AI Rewriter') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_youtube"
                    type="checkbox"
                    :checked="$setting->feature_ai_youtube == 1"
                    label="{{ __('AI YouTube') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_rss"
                    type="checkbox"
                    :checked="$setting->feature_ai_rss == 1"
                    label="{{ __('AI RSS') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="feature_ai_voice_clone"
                    type="checkbox"
                    :checked="$setting->feature_ai_voice_clone == 1"
                    label="{{ __('AI VoiceClone') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="team_functionality"
                    type="checkbox"
                    :checked="$setting->team_functionality == 1"
                    label="{{ __('Team Functionality') }}"
                    switcher
                />
                @if ($chatSetting)
                    <x-forms.input
                        class:container="mb-2"
                        id="chat_setting_for_customer"
                        type="checkbox"
                        :checked="setting('chat_setting_for_customer', '1') == '1'"
                        label="{{ __('Chat Setting (Extension)') }}"
                        switcher
                    />
                @endif
                <x-forms.input
                    class:container="mb-2"
                    id="user_prompt_library"
                    type="checkbox"
                    :checked="setting('user_prompt_library') == 1 || setting('user_prompt_library') == null"
                    label="{{ __('AI Chat Prompt Library') }}"
                    switcher
                />
                <x-forms.input
                    class:container="mb-2"
                    id="user_ai_image_prompt_library"
                    type="checkbox"
                    :checked="setting('user_ai_image_prompt_library') == 1 || setting('user_ai_image_prompt_library') == null"
                    label="{{ __('AI Image Prompt Library') }}"
                    switcher
                />
                @includeIf('default.panel.admin.settings.particles.photo-studio-general-setting')
            </div>
        </div>
        <h3 class="mb-[25px] mt-7 text-[20px]">{{ __('Users API Key Option') }}</h3>
        <div class="row mb-4">
            <div class="mb-3">
                <div class="form-label">
                    {{ __('Upon activating this feature, the admin API key will be deactivated, and users will need to input their own API keys for continued functionality.') }}
                </div>
                <x-forms.input
                    id="user_api_option"
                    type="checkbox"
                    :checked="$setting?->user_api_option == 1"
                    switcher
                    label="{{ __('Convert To Users Api') }}"
                />
            </div>
        </div>

        <h3 class="mb-[25px] mt-7 text-[20px]">{{ __('Mobile Settings') }}</h3>
        <div class="row mb-4">
            <div class="mb-3">
                <div class="form-label">
                    {{ __('This setting is in early alpha stage. Please do not activate until offically released.') }}
                </div>
                <x-forms.input
                    id="mobile_payment_active"
                    type="checkbox"
                    :checked="$setting?->mobile_payment_active == 1"
                    switcher
                    label="{{ __('Mobile Payment') }}"
                />
            </div>
        </div>

        <x-button
            class="w-full"
            id="settings_button"
            size="lg"
            type="submit"
        >
            {{ __('Save') }}
        </x-button>
    </form>

    <div
        class="modal fade"
        id="cropImagePop"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button
                        class="close"
                        data-bs-dismiss="modal"
                        type="button"
                        aria-label="Close"
                    ><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body text-center">
                    <div
                        class="center-block"
                        id="upload-demo"
                    ></div>
                </div>
                <div class="modal-footer">
                    <button
                        class="btn btn-default"
                        data-bs-dismiss="modal"
                        type="button"
                    >{{ __('Cancel and upload the image without cropping') }}</button>
                    <button
                        class="btn btn-primary"
                        id="cropImageBtn"
                        type="button"
                    >{{ __('Crop') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
    <script
        src="{{ custom_theme_url('/assets/libs/ace/src-min-noconflict/ace.js') }}"
        type="text/javascript"
        charset="utf-8"
    ></script>
    <script src="{{ custom_theme_url('https://foliotek.github.io/Croppie/croppie.js') }}"></script>

    <style
        type="text/css"
        media="screen"
    >
        .ace_editor {
            min-height: 200px;
        }
    </style>
    <script>
        var dashboard_code_before_head = ace.edit("dashboard_code_before_head");
        dashboard_code_before_head.session.setMode("ace/mode/html");

        var dashboard_code_before_body = ace.edit("dashboard_code_before_body");
        dashboard_code_before_body.session.setMode("ace/mode/html");
    </script>
    <script>
        function handleSelectChangeLang(type) {
            var selectElement = type === "meta_title" ? document.getElementById("metaTitleLocal") : document.getElementById(
                "metaDescLocal");
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var lang = selectedOption.value;

            $.ajax({
                type: 'POST',
                url: "/dashboard/admin/settings/get-meta-content",
                data: {
                    type: type,
                    lang: lang
                },
                success: function(response) {
                    var content = response.content;
                    var inputId = response.type === "meta_title" ? "meta_title" : "meta_description";
                    if (content !== null) {
                        $("#" + inputId).val(content);
                    } else {
                        $("#" + inputId).val('');
                    }
                },
                error: function(data) {
                    var err = data.responseJSON.errors;
                    $.each(err, function(index, value) {
                        toastr.error(value);
                    });
                }
            });
        }
        // on page first load 
        handleSelectChangeLang('meta_title');
        handleSelectChangeLang('meta_desc');
    </script>
    <script>
        var $uploadCrop, tempFilename, rawImg, imageId;
        var viewportWidth = 160; // Default width
        var viewportHeight = 70; // Default height
        function readFile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.upload-demo').addClass('ready');
                    $('#cropImagePop').modal('show');
                    rawImg = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                swal("Sorry - you're browser doesn't support the FileReader API");
            }
        }
        $uploadCrop = $('#upload-demo').croppie({
            viewport: {
                width: viewportWidth,
                height: viewportHeight,
            },
            enforceBoundary: false,
            enableExif: true
        });
        $('#cropImagePop').on('shown.bs.modal', function() {
            $uploadCrop.croppie('bind', {
                url: rawImg
            }).then(function() {
                console.log('jQuery bind complete');
            });
        });
        $('.item-img, .item-img-x2').on('change', function() {
            if ($(this).hasClass('item-img-x2')) {
                viewportWidth = 320;
                viewportHeight = 140;
                $uploadCrop.croppie('destroy'); // Destroy the existing croppie instance
                $uploadCrop = $('#upload-demo').croppie({ // Recreate the croppie instance with new dimensions
                    viewport: {
                        width: viewportWidth,
                        height: viewportHeight,
                    },
                    enforceBoundary: false,
                    enableExif: true
                });
            } else {
                viewportWidth = 160;
                viewportHeight = 70;
                $uploadCrop.croppie('destroy'); // Destroy the existing croppie instance
                $uploadCrop = $('#upload-demo').croppie({ // Recreate the croppie instance with default dimensions
                    viewport: {
                        width: viewportWidth,
                        height: viewportHeight,
                    },
                    enforceBoundary: false,
                    enableExif: true
                });
            }
            imageId = $(this).data('id');
            tempFilename = $(this).val();
            $('#cancelCropBtn').data('id', imageId);
            readFile(this);
        });
        $('#cropImageBtn').on('click', function(ev) {
            $uploadCrop.croppie('result', {
                type: 'blob',
                size: {
                    width: viewportWidth,
                    height: viewportHeight
                }
            }).then(function(resp) {
                var newInput = document.createElement('input');
                newInput.type = 'file';
                newInput.className = 'form-control item-img';
                newInput.setAttribute('data-id', imageId);
                newInput.id = imageId;
                newInput.name = imageId;
                var file = new File([resp], 'cropped_image.png', {
                    type: 'image/png'
                });
                let container = new DataTransfer();
                container.items.add(file);
                newInput.files = container.files;
                $('#' + imageId).replaceWith(newInput);
                $('#cropImagePop').modal('hide');
            });
        });

        var limitCheckbox = document.getElementById('limit');
        var countField = document.getElementById('countField');
        limitCheckbox.addEventListener('change', function() {
            countField.style.display = limitCheckbox.checked ? '' : 'none';
        });

        var voice_limit_checkbox = document.getElementById('voice_limit');
        var voiceCountField = document.getElementById('voiceCountField');
        voice_limit_checkbox.addEventListener('change', function() {
            voiceCountField.style.display = voice_limit_checkbox.checked ? '' : 'none';
        });
    </script>
@endpush
