@php
    $disable_floating_menu = true;
    $google_fonts_string = '';
    $dashboard_scss_path = 'resources/views/' . get_theme() . '/scss/dashboard.scss';
    $app_js_path = 'resources/views/' . get_theme() . '/js/app.js';
    $wide_layout_px_class = Theme::getSetting('wideLayoutPaddingX', '');
    $theme_google_fonts = Theme::getSetting('dashboard.googleFonts');

    if (isset($wide_layout_px) && !empty($wide_layout_px)) {
        $wide_layout_px_class = $wide_layout_px;
    }

    $i = 0;
    foreach ($theme_google_fonts as $font_name => $weights) {
        $font_string = 'family=' . str_replace(' ', '+', $font_name);
        if (!empty($weights)) {
            $font_string .= ':wght@' . implode(';', $weights);
        }
        $google_fonts_string .= $font_string . ($i === count($theme_google_fonts) - 1 ? '' : '&');
        $i++;
    }
@endphp

<!doctype html>
<html
    class="scroll-smooth"
    lang="{{ LaravelLocalization::getCurrentLocale() }}"
    dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}"
>

<head>
    @if (isset($setting->google_analytics_code))
        <!-- Google tag (gtag.js) -->
        <script
            async
            src="https://www.googletagmanager.com/gtag/js?id={{ $setting->google_analytics_code }}"
        ></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', '{{ $setting->google_analytics_code }}');
        </script>
    @endif

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover"
    />
    <meta
        http-equiv="X-UA-Compatible"
        content="ie=edge"
    />
    <meta
        name="description"
        content="{{ getMetaDesc($setting) }}"
    >
    @if (isset($setting->meta_keywords))
        <meta
            name="keywords"
            content="{{ $setting->meta_keywords }}"
        >
    @endif
    <link
        rel="icon"
        href="{{ custom_theme_url($setting->favicon_path ?? 'assets/favicon.ico', true) }}"
    >
    {{-- <title>{{ $setting->site_name }} | @yield('title')</title> --}}
    <title>
        {{ getMetaTitle($setting, ' ') ?? $setting->site_name }} | @yield('title')
    </title>

    @if (filled($google_fonts_string))
        <link
            rel="preconnect"
            href="https://fonts.googleapis.com"
        >
        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin
        >
        <link
            href="https://fonts.googleapis.com/css2?{{ $google_fonts_string }}&display=swap"
            rel="stylesheet"
        >
    @endif

    {{-- Liquid global params --}}
    <script>
        window.liquid = {
            assetsPath: '{{ url(custom_theme_url('assets')) }}',
        };
    </script>

    <!-- CSS files -->
    @if (!isset($disable_tblr) && empty($disable_tblr))
        <link
            href="{{ custom_theme_url('/assets/css/tabler.css') }}"
            rel="stylesheet"
        />
        <link
            href="{{ custom_theme_url('/assets/css/tabler-vendors.css') }}"
            rel="stylesheet"
        />
    @endif
    <link
        href="{{ custom_theme_url('/assets/libs/toastr/toastr.min.css') }}"
        rel="stylesheet"
    />

    @yield('additional_css')
    @stack('css')

    @vite($dashboard_scss_path)
    @if ($setting->dashboard_code_before_head != null)
        {!! $setting->dashboard_code_before_head !!}
    @endif

    @vite($app_js_path)

    @if (setting('additional_custom_css') != null)
        {!! setting('additional_custom_css') !!}
    @endif
</head>

<body class="group/body bg-background font-body text-xs text-foreground antialiased transition-bg">
    @includeIf('panel.layout.after-body-open')

    <script>
        const lqdDarkMode = localStorage.getItem('lqdDarkMode');
        const navbarIsShrinked = localStorage.getItem('lqdNavbarShrinked');

        document.body.classList.toggle('theme-dark', lqdDarkMode == 'true');
        document.body.classList.toggle('theme-light', lqdDarkMode != 'true');

        if (navbarIsShrinked === 'true') {
            document.body.classList.add('navbar-shrinked');
        }
    </script>

    <div
        class="pointer-events-none invisible fixed left-0 right-0 top-0 z-[999] opacity-0 transition-opacity"
        id="app-loading-indicator"
        x-data
        :class="{ 'opacity-0': !$store.appLoadingIndicator.showing, 'invisible': !$store.appLoadingIndicator.showing }"
    >
        <div class="lqd-progress relative h-[3px] w-full bg-foreground/10">
            <div class="lqd-progress-bar lqd-progress-bar-indeterminate lqd-app-loading-indicator-progress-bar absolute inset-0 bg-primary dark:bg-heading-foreground">
            </div>
        </div>
    </div>

    <div class="lqd-page relative flex min-h-full flex-col">

        <div @class(['lqd-page-wrapper flex grow-1'])>
            @auth
                @if (!isset($disable_navbar))
                    @include('panel.layout.navbar')
                @endif
            @endauth
            <div class="lqd-page-content-wrap flex grow flex-col overflow-hidden">
                @if ($good_for_now)
                    @auth
                        @if (!isset($disable_header))
                            @include('panel.layout.header', ['layout_wide', isset($layout_wide) ? $layout_wide : ''])
                        @endif
                        @if (!isset($disable_titlebar))
                            @include('panel.layout.titlebar', ['layout_wide', isset($layout_wide) ? $layout_wide : ''])
                        @endif
                    @endauth
                    @yield('before_content_container')
                    <div @class([
                        'lqd-page-content-container',
                        'h-full',
                        'container' => !isset($layout_wide) || empty($layout_wide),
                        $wide_layout_px_class =>
                            filled($wide_layout_px_class) &&
                            (isset($layout_wide) && !empty($layout_wide)),
                    ])>
                        @yield('content')
                    </div>
                @elseif(Auth::check() && !$good_for_now && Route::currentRouteName() != 'dashboard.admin.settings.general')
                    <div @class([
                        'lqd-page-content-container',
                        'container' => !isset($layout_wide) || empty($layout_wide),
                        $wide_layout_px_class =>
                            filled($wide_layout_px_class) &&
                            (isset($layout_wide) && !empty($layout_wide)),
                    ])>
                        @include('vendor.installer.magicai_c4st_Act')
                    </div>
                @else
                    @auth
                        @if (!isset($disable_header))
                            @include('panel.layout.header', ['layout_wide', isset($layout_wide) ? $layout_wide : ''])
                        @endif
                        @if (!isset($disable_titlebar))
                            @include('panel.layout.titlebar', ['layout_wide', isset($layout_wide) ? $layout_wide : ''])
                        @endif
                    @endauth
                    @yield('before_content_container')
                    <div @class([
                        'lqd-page-content-container',
                        'container' => !isset($layout_wide) || empty($layout_wide),
                        $wide_layout_px_class =>
                            filled($wide_layout_px_class) &&
                            (isset($layout_wide) && !empty($layout_wide)),
                    ])>
                        @yield('content')
                    </div>
                @endif

                @auth
                    @if (!isset($disable_footer))
                        @include('panel.layout.footer')
                    @endif
                @endauth
            </div>
        </div>
    </div>

    @auth
        @if (!isset($disable_floating_menu))
            <x-floating-menu />
        @endif
        @if (!isset($disable_mobile_bottom_menu))
            <x-bottom-menu />
        @endif
    @endauth

    @includeWhen(in_array($settings_two->chatbot_status, ['dashboard', 'both']) &&
            !activeRoute('dashboard.user.openai.chat.list', 'dashboard.user.openai.chat.chat', 'dashboard.user.openai.webchat.workbook') &&
            !(route('dashboard.user.openai.generator.workbook', 'ai_vision') == url()->current()) &&
            !(route('dashboard.user.openai.generator.workbook', 'ai_chat_image') == url()->current()) &&
            !(route('dashboard.user.openai.generator.workbook', 'ai_pdf') == url()->current()),
        'panel.chatbot.widget')

    @include('panel.layout.scripts')

    @if (\Session::has('message'))
        <script>
            toastr.{{ \Session::get('type') }}('{{ \Session::get('message') }}')
        </script>
    @endif

    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                toastr.error('{{ $error }}');
            @endforeach
        </script>
    @endif

    @stack('script')

    <script src="{{ custom_theme_url('/assets/js/frontend.js') }}"></script>

    @if ($setting->dashboard_code_before_body != null)
        {!! $setting->dashboard_code_before_body !!}
    @endif

    @auth()
        @if (\Illuminate\Support\Facades\Auth::user()->type == 'admin')
            <script src="{{ custom_theme_url('/assets/js/panel/update-check.js') }}"></script>
        @endif
    @endauth

    <script src="{{ custom_theme_url('assets/js/chatbot.js') }}"></script>
    <template id="typing-template">
        <div class="lqd-typing relative inline-flex items-center gap-3 rounded-full bg-secondary !px-3 !py-2 text-xs font-medium leading-none text-secondary-foreground">
            {{ __('Typing') }}
            <div class="lqd-typing-dots flex h-5 items-center gap-1">
                <span class="lqd-typing-dot inline-block !h-1 !w-1 rounded-full !bg-current opacity-40 ![animation-delay:0.2s]"></span>
                <span class="lqd-typing-dot inline-block !h-1 !w-1 rounded-full !bg-current opacity-60 ![animation-delay:0.3s]"></span>
                <span class="lqd-typing-dot inline-block !h-1 !w-1 rounded-full !bg-current opacity-80 ![animation-delay:0.4s]"></span>
            </div>
        </div>
    </template>

    @includeIf('panel.layout.before-body-close')

    @if (view()->exists('panel.admin.settings.particles.serper_seo'))
        <script src="{{ custom_theme_url('/assets/js/panel/generateSEO.js') }}"></script>
    @endif
</body>

</html>
