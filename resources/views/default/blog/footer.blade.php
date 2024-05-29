<footer class="site-footer relative bg-black pb-11 pt-40 text-white">
    <div
        class="absolute inset-0"
        style="background: radial-gradient(circle at 0% -20%, #a12a91, rgba(33, 13, 123, 0.83), transparent, transparent, transparent)"
    >
    </div>
    <div class="absolute inset-x-0 -top-px">
        <svg
            class="w-full fill-background"
            preserveAspectRatio="none"
            width="1440"
            height="86"
            viewBox="0 0 1440 86"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path d="M0 85.662C240 29.1253 480 0.857 720 0.857C960 0.857 1200 29.1253 1440 85.662V0H0V85.662Z" />
        </svg>
    </div>
    <div class="relative">
        <div class="container mb-28">
            <div class="mx-auto w-1/2 text-center max-lg:w-10/12 max-sm:w-full">
                <p class="mb-9 text-[10px] font-semibold uppercase tracking-widest"><span
                        class="!me-2 inline-block rounded-xl bg-[#262626] px-3 py-1">{{ __($setting->site_name) }}</span>
                    {{ __($fSetting->footer_text_small) }}</p>
                <p
                    class="font-oneset -from-[5%] mb-8 bg-gradient-to-br from-transparent to-white to-50% bg-clip-text text-[100px] font-bold leading-none tracking-tight text-transparent max-sm:text-[18vw]">
                    {{ __($fSetting->footer_header) }}</p>
                <p class="font-oneset mb-9 px-10 text-[20px] font-normal leading-[1.25em] opacity-50">
                    {{ __($fSetting->footer_text) }}</p>
                <a
                    class="relative inline-flex items-center overflow-hidden rounded-xl border-[2px] border-white border-opacity-0 bg-white/10 px-7 py-4 font-semibold transition-all duration-300 hover:scale-105 hover:border-white hover:bg-white hover:bg-opacity-100 hover:text-black hover:shadow-lg"
                    href="{{ !empty($fSetting->footer_button_url) ? $fSetting->footer_button_url : '#' }}"
                    target="_blank"
                >
                    {!! __($fSetting->footer_button_text) !!}
                    <span class="relative z-10 ms-2 inline-flex items-center">
                        <svg
                            width="11"
                            height="14"
                            viewBox="0 0 47 62"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M27.95 0L0 38.213H18.633V61.141L46.583 22.928H27.95V0Z" />
                        </svg>
                    </span>
                </a>
                <x-button
                    link="{{ $fSetting->footer_button_url }}"
                    label="{{ __($fSetting->footer_button_text) }}"
                    size="lg"
                    iconPos="end"
                    bg="bg-white bg-opacity-10 border-[2px] border-white border-opacity-0 hover:bg-white hover:bg-opacity-100"
                    text="hover:!text-black"
                >
                    <x-slot name="icon">
                        <svg
                            class="ml-2"
                            width="11"
                            height="14"
                            viewBox="0 0 47 62"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M27.95 0L0 38.213H18.633V61.141L46.583 22.928H27.95V0Z" />
                        </svg>
                    </x-slot>
                </x-button>
            </div>
        </div>
        <hr class="border-white border-opacity-15">
        <div class="container">
            <div class="flex flex-wrap items-center justify-between gap-8 pb-7 pt-10 max-sm:justify-center">
                <a href="{{ route('index') }}">
                    @if (isset($setting->logo_2x_path))
                        <img
                            src="{{ custom_theme_url($setting->logo_path, true) }}"
                            srcset="/{{ $setting->logo_2x_path }} 2x"
                            alt="{{ $setting->site_name }} logo"
                        >
                    @else
                        <img
                            src="{{ custom_theme_url($setting->logo_path, true) }}"
                            alt="{{ $setting->site_name }} logo"
                        >
                    @endif
                </a>
                <ul class="flex flex-wrap items-center gap-7 text-[14px] max-sm:justify-center">
                    @if ($setting->frontend_footer_instagram != null)
                        <li>
                            <a
                                class="inline-flex items-center gap-2"
                                href="{{ $setting->frontend_footer_instagram }}"
                            >
                                <svg
                                    width="14"
                                    height="14"
                                    viewBox="0 0 14 14"
                                    fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M6.565 3.897C6.12304 3.89674 5.68536 3.98364 5.27703 4.15275C4.8687 4.32185 4.49773 4.56982 4.18535 4.88247C3.87298 5.19513 3.62533 5.56632 3.4566 5.9748C3.28786 6.38329 3.20134 6.82104 3.202 7.263C3.20134 7.70496 3.28786 8.14272 3.4566 8.5512C3.62533 8.95968 3.87298 9.33087 4.18535 9.64353C4.49773 9.95618 4.8687 10.2042 5.27703 10.3733C5.68536 10.5424 6.12304 10.6293 6.565 10.629C7.00747 10.6301 7.44579 10.5437 7.85481 10.375C8.26384 10.2062 8.63552 9.95839 8.94853 9.64566C9.26154 9.33292 9.50972 8.96146 9.67884 8.55259C9.84795 8.14371 9.93466 7.70547 9.934 7.263C9.93466 6.82053 9.84795 6.38229 9.67884 5.97342C9.50972 5.56454 9.26154 5.19308 8.94853 4.88035C8.63552 4.56762 8.26384 4.31977 7.85481 4.15102C7.44579 3.98227 7.00747 3.89595 6.565 3.897ZM6.565 9.452C5.98521 9.45042 5.42966 9.21918 5.01996 8.80892C4.61026 8.39867 4.37979 7.8428 4.379 7.263C4.37979 6.6833 4.61029 6.12753 5.02002 5.71743C5.42975 5.30733 5.9853 5.07632 6.565 5.075C7.14557 5.075 7.70241 5.30543 8.11321 5.71567C8.52402 6.12591 8.75521 6.68243 8.756 7.263C8.75521 7.84366 8.52405 8.40028 8.11327 8.81069C7.7025 9.22109 7.14566 9.45174 6.565 9.452ZM10.857 3.759C10.857 3.55081 10.7743 3.35114 10.6271 3.20392C10.4799 3.05671 10.2802 2.974 10.072 2.974C9.86381 2.974 9.66414 3.05671 9.51692 3.20392C9.36971 3.35114 9.287 3.55081 9.287 3.759C9.28674 3.86216 9.30686 3.96436 9.34622 4.05972C9.38558 4.15508 9.44339 4.24172 9.51633 4.31467C9.58928 4.38761 9.67592 4.44543 9.77128 4.48478C9.86664 4.52414 9.96884 4.54427 10.072 4.544C10.1752 4.54427 10.2774 4.52414 10.3727 4.48478C10.4681 4.44543 10.5547 4.38761 10.6277 4.31467C10.7006 4.24172 10.7584 4.15508 10.7978 4.05972C10.8371 3.96436 10.8573 3.86216 10.857 3.759ZM13.086 4.559C13.1076 3.53821 12.7264 2.54995 12.025 1.808C11.2821 1.10849 10.2952 0.727024 9.275 0.745002C8.191 0.683002 4.942 0.683002 3.858 0.745002C2.838 0.724773 1.85059 1.10452 1.107 1.803C0.407211 2.54558 0.02631 3.53283 0.0460018 4.553C-0.0149982 5.637 -0.0149982 8.891 0.0460018 9.97C0.024539 10.9908 0.405671 11.979 1.107 12.721C1.85075 13.4196 2.83779 13.8002 3.858 13.782C4.942 13.844 8.191 13.844 9.275 13.782C10.2958 13.8035 11.284 13.4223 12.026 12.721C12.7247 11.9774 13.1051 10.9902 13.086 9.97C13.148 8.891 13.148 5.64 13.086 4.556V4.559ZM11.686 11.136C11.5742 11.4179 11.4058 11.6738 11.1911 11.8879C10.9765 12.1021 10.7201 12.2699 10.438 12.381C9.16472 12.6426 7.862 12.7314 6.565 12.645C5.27 12.7295 3.96951 12.6407 2.698 12.381C2.41558 12.2696 2.15908 12.1013 1.9444 11.8866C1.72971 11.6719 1.56144 11.4154 1.45 11.133C1.1887 9.8607 1.0999 8.559 1.186 7.263C1.10109 5.96634 1.18987 4.66414 1.45 3.391C1.56144 3.10858 1.72971 2.85208 1.9444 2.63739C2.15908 2.42271 2.41558 2.25443 2.698 2.143C3.96942 1.88264 5.27009 1.79451 6.565 1.881C7.861 1.79626 9.16251 1.88505 10.435 2.145C10.7178 2.25587 10.9748 2.42368 11.19 2.63803C11.4052 2.85237 11.574 3.10867 11.686 3.391C11.9473 4.6633 12.0361 5.965 11.95 7.261C12.0364 8.55767 11.9476 9.86006 11.686 11.133V11.136Z"
                                    />
                                </svg>
                                {{ __('Instagram') }}
                            </a>
                        </li>
                    @endif
                    @if ($setting->frontend_footer_twitter != null)
                        <li>
                            <a
                                class="inline-flex items-center gap-2"
                                href="{{ $setting->frontend_footer_twitter }}"
                            >
                                <svg
                                    width="16"
                                    height="13"
                                    viewBox="0 0 16 13"
                                    fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M13.523 3.211C14.1228 2.77154 14.6441 2.23399 15.065 1.621C14.502 1.86701 13.9054 2.02745 13.295 2.097C13.9369 1.71577 14.4176 1.11344 14.647 0.403004C14.0446 0.761467 13.3844 1.01253 12.696 1.145C12.4083 0.837679 12.0604 0.59287 11.674 0.425818C11.2876 0.258766 10.871 0.173049 10.45 0.174004C10.0461 0.173741 9.64613 0.253099 9.27294 0.407539C8.89974 0.561979 8.56066 0.788472 8.27506 1.07406C7.98947 1.35966 7.76298 1.69875 7.60854 2.07194C7.4541 2.44514 7.37474 2.84511 7.375 3.249C7.37654 3.48431 7.402 3.71885 7.451 3.949C6.22796 3.88692 5.03162 3.56845 3.93958 3.01425C2.84754 2.46005 1.88419 1.68249 1.112 0.732003C0.834894 1.20462 0.690161 1.74314 0.693002 2.291C0.69329 2.79727 0.818693 3.29561 1.05806 3.74171C1.29742 4.18781 1.64332 4.56785 2.065 4.848C1.57743 4.82921 1.10119 4.69558 0.675003 4.458V4.491C0.673081 5.2019 0.917536 5.89151 1.36677 6.44248C1.81601 6.99345 2.44227 7.37175 3.139 7.513C2.8746 7.58015 2.6028 7.61374 2.33 7.613C2.13541 7.61162 1.94119 7.59557 1.749 7.565C1.94555 8.17652 2.32836 8.71128 2.84387 9.09446C3.35938 9.47764 3.98179 9.69006 4.624 9.702C3.53536 10.5562 2.19073 11.0187 0.807003 11.015C0.55914 11.0168 0.311384 11.0041 0.0650024 10.977C1.47289 11.8822 3.11226 12.3614 4.786 12.357C5.93682 12.365 7.07775 12.1442 8.14252 11.7075C9.20729 11.2708 10.1747 10.6269 10.9885 9.8132C11.8023 8.99948 12.4463 8.03216 12.8831 6.96744C13.3199 5.90272 13.5408 4.76182 13.533 3.611C13.533 3.477 13.533 3.344 13.523 3.211Z"
                                    />
                                </svg>
                                {{ __('Twitter') }}
                            </a>
                        </li>
                    @endif
                    @if ($setting->frontend_footer_facebook != null)
                        <li>
                            <a
                                class="inline-flex items-center gap-2"
                                href="{{ $setting->frontend_footer_facebook }}"
                            >
                                <svg
                                    width="15"
                                    height="15"
                                    viewBox="0 0 15 15"
                                    fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M14.831 7.266C14.8313 6.31174 14.6435 5.36678 14.2784 4.48511C13.9134 3.60344 13.3782 2.80234 12.7034 2.12758C12.0287 1.45281 11.2276 0.917614 10.3459 0.552556C9.46421 0.187499 8.51925 -0.000262497 7.565 2.75432e-07C6.61074 -0.000262497 5.66578 0.187499 4.78411 0.552556C3.90244 0.917614 3.10133 1.45281 2.42657 2.12758C1.75181 2.80234 1.21661 3.60344 0.851552 4.48511C0.486495 5.36678 0.298733 6.31174 0.298996 7.266C0.299081 8.99629 0.916469 10.6698 2.04014 11.9856C3.16381 13.3013 4.72005 14.1731 6.429 14.444V9.366H4.584V7.266H6.43V5.666C6.39018 5.29215 6.43309 4.91411 6.5557 4.5587C6.67831 4.20329 6.87761 3.8792 7.13947 3.60942C7.40133 3.33964 7.71933 3.13078 8.07093 2.99764C8.42254 2.86449 8.79912 2.81033 9.174 2.839C9.71907 2.84661 10.2628 2.89407 10.801 2.981V4.768H9.884C9.72795 4.74729 9.56924 4.76193 9.41961 4.81084C9.26998 4.85976 9.13327 4.94169 9.01958 5.05057C8.90589 5.15946 8.81814 5.29251 8.76283 5.43989C8.70751 5.58727 8.68603 5.7452 8.7 5.902V7.266H10.715L10.393 9.366H8.7V14.444C10.4091 14.1733 11.9656 13.3017 13.0895 11.9859C14.2133 10.6701 14.8309 8.99644 14.831 7.266Z"
                                    />
                                </svg>
                                {{ __('Facebook') }}
                            </a>
                        </li>
                    @endif

                </ul>
            </div>
            <hr class="border-white border-opacity-15">
            <div class="flex flex-wrap items-center justify-center gap-4 py-9 max-sm:text-center">
                <p class="!text-end text-[14px] opacity-60">{{ __($fSetting->footer_copyright) }}</p>
            </div>
        </div>
    </div>
</footer>

@if ($setting->gdpr_status == 1)
    <div
        class="fixed bottom-12 left-1/2 z-50 -translate-x-1/2 rounded-full bg-white p-2 drop-shadow-2xl max-sm:w-11/12"
        id="gdpr"
    >
        <div class="flex items-center justify-between gap-6 text-sm">
            <div class="content-left pl-4">
                {!! __($setting->gdpr_content) !!}
            </div>
            <div class="content-right text-end">
                <button class="cursor-pointer rounded-full bg-black px-4 py-2 text-white">{!! __($setting->gdpr_button) !!}</button>
            </div>
        </div>
    </div>
@endif
