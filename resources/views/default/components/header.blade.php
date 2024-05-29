@php
    $user_avatar = Auth::user()->avatar;

    if (!Auth::user()->github_token && !Auth::user()->google_token && !Auth::user()->facebook_token) {
        $user_avatar = '/' . $user_avatar;
    }
@endphp

<header class="lqd-header relative flex h-[--header-height] border-b border-header-border bg-header-background text-xs font-medium transition-colors max-lg:h-[65px]">
    <div @class([
        'lqd-header-container flex w-full grow gap-2 px-4 max-lg:w-full max-lg:max-w-none',
        'container' => !$attributes->get('layout-wide'),
        Theme::getSetting('wideLayoutPaddingX', '') =>
            filled(Theme::getSetting('wideLayoutPaddingX', '')) &&
            $attributes->get('layout-wide'),
    ])>

        {{-- Mobile nav toggle and logo --}}
        <div class="mobile-nav-logo flex items-center gap-3 lg:hidden">
            <button
                class="lqd-mobile-nav-toggle size-10 flex items-center justify-center"
                type="button"
                x-init
                @click.prevent="$store.mobileNav.toggleNav()"
                :class="{ 'lqd-is-active': !$store.mobileNav.navCollapse }"
            >
                <span class="lqd-mobile-nav-toggle-icon relative h-[2px] w-5 rounded-xl bg-current"></span>
            </button>
            <a
                class="flex shrink-0 items-center justify-center max-md:max-w-[120px] lg:hidden lg:px-2"
                href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}"
            >
                @if (isset($setting->logo_dashboard))
                    <img
                        class="dark:hidden"
                        src="{{ custom_theme_url($setting->logo_dashboard_path, true) }}"
                        @if (isset($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                    <img
                        class="hidden dark:block"
                        src="{{ custom_theme_url($setting->logo_dashboard_dark_path, true) }}"
                        @if (isset($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                @else
                    <img
                        class="dark:hidden"
                        src="{{ custom_theme_url($setting->logo_path, true) }}"
                        @if (isset($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                    <img
                        class="hidden dark:block"
                        src="{{ custom_theme_url($setting->logo_dark_path, true) }}"
                        @if (isset($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                @endif
            </a>
        </div>

        {{-- Title slot --}}
        @if ($title ?? false)
            <div class="header-title-container peer/title hidden items-center lg:flex">
                <h1 class="m-0 font-semibold">
                    {{ $title }}
                </h1>
            </div>
        @endif

        {{-- Search form --}}
        <div class="header-search-container flex items-center peer-[&.header-title-container]/title:grow peer-[&.header-title-container]/title:justify-center">
            <x-header-search />
        </div>

        <div class="header-actions-container flex grow justify-end gap-4 max-lg:basis-2/3 max-lg:gap-2">

            {{-- Action buttons --}}
            @if ($actions ?? false)
                {{ $actions }}
            @else
                <div class="flex items-center max-xl:gap-2 max-lg:hidden xl:gap-3">
                    @if (Auth::user()->type == 'admin')
                        <x-button
                            href="{{ route('dashboard.admin.index') }}"
                            variant="ghost-shadow"
                        >
                            {{ __('Admin Panel') }}
                        </x-button>
                    @endif

                    @if ($settings_two->liquid_license_type == 'Extended License')
                        @if (getSubscriptionStatus())
                            <x-button
                                class="max-xl:hidden"
                                href="{{ route('dashboard.user.payment.subscription') }}"
                                variant="ghost-shadow"
                            >
                                {{ getSubscriptionName() }} - {{ getSubscriptionDaysLeft() }}
                                {{ __('Days Left') }}
                            </x-button>
                        @else
                            <x-button
                                class="max-xl:hidden"
                                href="{{ route('dashboard.user.payment.subscription') }}"
                                variant="ghost-shadow"
                            >
                                {{ __('No Active Subscription') }}
                            </x-button>
                        @endif

                        <x-button
                            class="max-xl:hidden"
                            href="{{ route('dashboard.user.payment.subscription') }}"
                        >
                            <x-tabler-bolt class="size-4 fill-current" />
                            <span class="max-lg:hidden">
                                {{ __('Upgrade') }}
                            </span>
                        </x-button>
                    @endif
                </div>
            @endif

            <div class="flex items-center gap-4 max-lg:gap-2">
                {{-- Dark/light switch --}}
                <x-light-dark-switch />

                {{-- Language dropdown --}}
                @if (count(explode(',', $settings_two->languages)) > 1)
                    <x-dropdown.dropdown
                        class="header-language-dropdown"
                        anchor="end"
                        offsetY="20px"
                    >
                        <x-slot:trigger
                            class="size-6 max-lg:size-10 max-lg:border max-lg:dark:bg-white/[3%]"
                            size="none"
                        >
                            <x-tabler-world stroke-width="1.5" />
                        </x-slot:trigger>

                        <x-slot:dropdown
                            class="overflow-hidden"
                        >
                            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                @if (in_array($localeCode, explode(',', $settings_two->languages)))
                                    <a
                                        class="flex items-center gap-2 border-b px-3 py-2 text-heading-foreground transition-colors last:border-b-0 hover:bg-foreground/5 hover:no-underline"
                                        rel="alternate"
                                        hreflang="{{ $localeCode }}"
                                        href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                                    >
                                        <span class="text-xl">
                                            {{ country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1)) }}
                                        </span>
                                        {{ $properties['native'] }}
                                    </a>
                                @endif
                            @endforeach
                        </x-slot:dropdown>
                    </x-dropdown.dropdown>
                @endif

                {{-- Upgrade button on mobile --}}
                <x-button
                    class="lqd-header-upgrade-btn size-10 flex items-center justify-center border p-0 text-current dark:bg-white/[3%] lg:hidden"
                    variant="link"
                    href="{{ route('dashboard.user.payment.subscription') }}"
                >
                    <x-tabler-bolt stroke-width="1.5" />
                </x-button>

                {{-- User menu --}}
                <x-dropdown.dropdown
                    class="header-user-dropdown"
                    anchor="end"
                    offsetY="20px"
                >
                    <x-slot:trigger
                        class="size-9 p-0"
                    >
                        <span
                            class="size-full inline-block rounded-full bg-cover"
                            style="background-image: url({{ custom_theme_url($user_avatar) }})"
                        ></span>
                    </x-slot:trigger>

                    <x-slot:dropdown
                        class="min-w-52"
                    >
                        <div class="px-3 pt-3 text-foreground/70">
                            <p>{{ Auth::user()->fullName() }}</p>
                            <p class="text-3xs">{{ Auth::user()->email }}</p>
                        </div>

                        <hr>

                        <x-remaining-credit class="flex-col-reverse px-3 py-1.5 text-2xs" />

                        <hr>

                        <div class="pb-2 text-2xs">
                            <a
                                class="flex w-full items-center px-3 py-2 hover:bg-foreground/5"
                                href="{{ route('dashboard.user.2fa.activate') }}"
                            >
                                {{ __('2-Factor Auth.') }}
                            </a>
                            <a
                                class="flex w-full items-center px-3 py-2 hover:bg-foreground/5"
                                href="{{ route('dashboard.user.payment.subscription') }}"
                            >
                                {{ __('Plan') }}
                            </a>
                            <a
                                class="flex w-full items-center px-3 py-2 hover:bg-foreground/5"
                                href="{{ route('dashboard.user.orders.index') }}"
                            >
                                {{ __('Orders') }}
                            </a>
                            <a
                                class="flex w-full items-center px-3 py-2 hover:bg-foreground/5"
                                href="{{ route('dashboard.user.settings.index') }}"
                            >
                                {{ __('Settings') }}
                            </a>
                            <form
                                class="flex w-full"
                                id="logout"
                                method="POST"
                                action="{{ route('logout') }}"
                            >
                                @csrf
                                <button
                                    class="flex w-full items-center px-3 py-2 hover:bg-foreground/10"
                                    type="submit"
                                >
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        </div>

                    </x-slot:dropdown>
                </x-dropdown.dropdown>
            </div>
        </div>
    </div>
</header>
