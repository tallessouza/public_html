@php
    $nav_links = [
        'Home' => '#premium-support',
        'All Access' => '#premium-support-access',
        'Premium Support' => '#premium-support-support',
        'Free Customization' => '#premium-support-customization',
    ];
@endphp

<header
    class="absolute inset-x-0 top-0 z-10"
    x-data="{ mobileMenuVisible: false }"
>
    <div class="grid grid-flow-col items-center px-10 py-8 max-xl:grid-cols-2 max-xl:py-5 max-sm:px-5 xl:[grid-template-columns:20%_minmax(580px,100%)_20%]">
        <a href="#">
            @if (isset($setting->logo_dashboard))
                <img
                    class="h-auto group-[.navbar-shrinked]/body:hidden dark:hidden"
                    src="{{ custom_theme_url($setting->logo_dashboard_path, true) }}"
                    @if (isset($setting->logo_dashboard_2x_path) && !empty($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
                <img
                    class="hidden h-auto group-[.navbar-shrinked]/body:hidden dark:block"
                    src="{{ custom_theme_url($setting->logo_dashboard_dark_path, true) }}"
                    @if (isset($setting->logo_dashboard_dark_2x_path) && !empty($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
            @else
                <img
                    class="h-auto group-[.navbar-shrinked]/body:hidden dark:hidden"
                    src="{{ custom_theme_url($setting->logo_path, true) }}"
                    @if (isset($setting->logo_2x_path) && !empty($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
                <img
                    class="hidden h-auto group-[.navbar-shrinked]/body:hidden dark:block"
                    src="{{ custom_theme_url($setting->logo_dark_path, true) }}"
                    @if (isset($setting->logo_dark_2x_path) && !empty($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
            @endif
        </a>

        <nav
            class="flex w-full justify-center whitespace-nowrap transition-all max-xl:invisible max-xl:absolute max-xl:inset-x-10 max-xl:top-full max-xl:w-auto max-xl:origin-top max-xl:scale-95 max-xl:opacity-0 max-sm:inset-x-5 max-xl:[&.lqd-is-active]:visible max-xl:[&.lqd-is-active]:scale-100 max-xl:[&.lqd-is-active]:opacity-100"
            :class="{ 'lqd-is-active': mobileMenuVisible }"
        >
            <ul
                class="flex gap-2 rounded-full border border-white/15 bg-black/15 px-4 py-3 leading-tight text-white/90 backdrop-blur-md max-xl:w-full max-xl:flex-col max-xl:items-center max-xl:gap-y-5 max-xl:rounded-xl max-xl:text-center">
                @foreach ($nav_links as $label => $href)
                    <li>
                        <a
                            class="px-6 py-1"
                            href="{{ $href }}"
                        >
                            @lang($label)
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="flex justify-end">
            <a
                class="flex items-center gap-2 rounded-full border border-white/15 bg-black/15 px-7 py-3 leading-tight text-white/90 backdrop-blur-md transition-all hover:scale-110 hover:bg-white hover:text-black max-xl:hidden"
                href="#premium-support-form-input"
            >
                <svg
                    width="16"
                    height="14"
                    viewBox="0 0 16 14"
                    fill="currentColor"
                    fill-opacity="0.8"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M13.167 5C12.542 5 12.0107 4.78125 11.5732 4.34375C11.1357 3.90625 10.917 3.375 10.917 2.75C10.917 2.125 11.1357 1.59375 11.5732 1.15625C12.0107 0.71875 12.542 0.5 13.167 0.5C13.792 0.5 14.3232 0.71875 14.7607 1.15625C15.1982 1.59375 15.417 2.125 15.417 2.75C15.417 3.375 15.1982 3.90625 14.7607 4.34375C14.3232 4.78125 13.792 5 13.167 5ZM1.91699 14C1.50449 14 1.15137 13.8531 0.857622 13.5594C0.563872 13.2656 0.416992 12.9125 0.416992 12.5V3.5C0.416992 3.0875 0.563872 2.73438 0.857622 2.44063C1.15137 2.14688 1.50449 2 1.91699 2H9.492C9.442 2.25 9.417 2.5 9.417 2.75C9.417 3 9.442 3.25 9.492 3.5C9.5795 3.9 9.7232 4.27188 9.9232 4.61563C10.1232 4.95938 10.367 5.2625 10.6545 5.525L7.91699 7.25L1.91699 3.5V5L7.91699 8.75L11.8732 6.275C12.0857 6.35 12.2982 6.40625 12.5107 6.44375C12.7232 6.48125 12.942 6.5 13.167 6.5C13.567 6.5 13.9607 6.4375 14.3482 6.3125C14.7357 6.1875 15.092 6 15.417 5.75V12.5C15.417 12.9125 15.2701 13.2656 14.9764 13.5594C14.6826 13.8531 14.3295 14 13.917 14H1.91699Z"
                    />
                </svg>

                @lang('Notify Me')
            </a>
            <button
                class="size-12 group inline-flex items-center justify-center rounded-full border border-white/15 bg-black/15 text-white xl:hidden"
                @click.prvent="mobileMenuVisible = !mobileMenuVisible"
                :class="{ 'lqd-is-active': mobileMenuVisible }"
            >
                <x-tabler-x class="size-5 hidden group-[&.lqd-is-active]:block" />
                <x-tabler-menu-deep class="size-5 group-[&.lqd-is-active]:hidden" />
            </button>
        </div>
    </div>
</header>
