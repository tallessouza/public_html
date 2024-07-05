@php
    $team = Auth::user()->getAttribute('team');
    $teamManager = Auth::user()->getAttribute('teamManager');
@endphp

@if ($team)
    <x-card
        class:head="border-b-0 pb-0 pt-8 px-8"
        class:body="px-8 pt-5 pb-8"
        class="min-h-full text-xs max-md:text-center"
        szie="lg"
    >
        <x-slot:head>
            <h2 class="m-0 flex items-center gap-4">
                {{-- blade-formatter-disable --}}
				<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
					<rect width="40" height="40" rx="8" fill="#6244BB" fill-opacity="0.12"/>
					<path d="M27.6634 21.0785C27.3243 21.0785 27.0401 20.8219 27.0034 20.4827C26.7834 18.466 25.7018 16.651 24.0334 15.496C23.7309 15.2852 23.6576 14.8727 23.8684 14.5702C24.0793 14.2677 24.4918 14.1944 24.7943 14.4052C26.7834 15.7894 28.0668 17.9527 28.3326 20.3452C28.3693 20.7119 28.1034 21.0419 27.7368 21.0785C27.7093 21.0785 27.6909 21.0785 27.6634 21.0785Z" fill="#6244BB"/>
					<path d="M23.6576 28.7511C22.5301 29.2919 21.3201 29.5669 20.0551 29.5669C18.7351 29.5669 17.4793 29.2736 16.3059 28.6777C15.9759 28.5219 15.8476 28.1186 16.0126 27.7886C16.1684 27.4586 16.5718 27.3302 16.9018 27.4861C17.4793 27.7794 18.0934 27.9811 18.7168 28.1002C19.5601 28.2652 20.4218 28.2744 21.2651 28.1277C21.8884 28.0177 22.5026 27.8252 23.0709 27.5502C23.4101 27.3944 23.8134 27.5227 23.9601 27.8619C24.1251 28.1919 23.9968 28.5952 23.6576 28.7511Z" fill="#6244BB"/>
					<path d="M12.4284 20.715C12.4101 20.715 12.3826 20.715 12.3642 20.715C11.9976 20.6784 11.7317 20.3484 11.7684 19.9817C12.0159 17.5892 13.2809 15.4258 15.2517 14.0325C15.5451 13.8217 15.9667 13.895 16.1776 14.1883C16.3884 14.4908 16.3151 14.9033 16.0217 15.1142C14.3717 16.2875 13.2992 18.1025 13.0976 20.11C13.0609 20.4584 12.7676 20.715 12.4284 20.715Z" fill="#6244BB"/>
					<path d="M20.0458 10.8425C18.625 10.8425 17.4608 11.9975 17.4608 13.4275C17.4608 14.8575 18.6158 16.0125 20.0458 16.0125C21.4758 16.0125 22.6308 14.8575 22.6308 13.4275C22.6308 11.9975 21.4758 10.8425 20.0458 10.8425Z" fill="#6244BB"/>
					<path d="M13.6292 21.7142C12.2083 21.7142 11.0442 22.8692 11.0442 24.2992C11.0442 25.7292 12.1992 26.8842 13.6292 26.8842C15.0592 26.8842 16.2142 25.7292 16.2142 24.2992C16.2142 22.8692 15.05 21.7142 13.6292 21.7142Z" fill="#6244BB"/>
					<path d="M26.3708 21.7142C24.9499 21.7142 23.7858 22.8692 23.7858 24.2992C23.7858 25.7292 24.9408 26.8842 26.3708 26.8842C27.8008 26.8842 28.9558 25.7292 28.9558 24.2992C28.9558 22.8692 27.8008 21.7142 26.3708 21.7142Z" fill="#6244BB"/>
				</svg>
				{{-- blade-formatter-enable --}}
                {{ __('Active Workspace:') }}
            </h2>
        </x-slot:head>

        <div class="flex flex-col items-center gap-y-4 font-medium leading-normal">
            <div>
                <h3 class="mb-4 font-bold">
                    {{ $teamManager->name . ' ' . $teamManager->surname }}
                    <x-badge class="ms-2 text-2xs">
                        @lang('Team Manager')
                    </x-badge>
                </h3>

                <p>
                    @lang("You have the Team plan which has a remaining balance of <strong class='font-bold text-heading-foreground'>:word</strong> words and <strong class='font-bold text-heading-foreground'>:image</strong> images.", ['word' => Auth::user()->remaining_words, 'image' => Auth::user()->remaining_images])
                </p>
            </div>
            <div>
                <div class="relative">
                    <h3 class="absolute start-1/2 top-[calc(50%-5px)] m-0 -translate-x-1/2 text-center text-xs font-normal">
                        <strong class="text-[1.75em] font-semibold leading-none text-heading-foreground max-sm:text-[1.5em]">
                            @if (Auth::user()->remaining_words == -1)
                                {{ __('Unlimited') }}
                            @else
                                {{ number_format((int) Auth::user()->remaining_words) }}
                            @endif
                        </strong>
                        <br>
                        {{ __('Tokens') }}
                    </h3>
                    <div
                        class="relative [&_.apexcharts-legend-text]:!m-0 [&_.apexcharts-legend-text]:!pe-2 [&_.apexcharts-legend-text]:ps-2 [&_.apexcharts-legend-text]:!text-foreground"
                        id="chart-credit"
                    >
                    </div>
                </div>
            </div>
            <div>
                <p>
                    @lang('You can contact your team manager if you need more credits.')
                </p>
            </div>
        </div>
    </x-card>
@else
    <x-card
        class:head="border-b-0 pt-8 pb-0 px-8"
        class:body="flex flex-col items-center justify-between px-8 pt-5 pb-8"
        class="min-h-full text-xs"
        size="lg"
    >
        <x-slot:head>
            <h2 class="m-0 flex items-center gap-4">
                {{-- blade-formatter-disable --}}
				<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
					<rect width="40" height="40" rx="8" fill="#6244BB" fill-opacity="0.12"/>
					<path d="M27.6634 21.0785C27.3243 21.0785 27.0401 20.8219 27.0034 20.4827C26.7834 18.466 25.7018 16.651 24.0334 15.496C23.7309 15.2852 23.6576 14.8727 23.8684 14.5702C24.0793 14.2677 24.4918 14.1944 24.7943 14.4052C26.7834 15.7894 28.0668 17.9527 28.3326 20.3452C28.3693 20.7119 28.1034 21.0419 27.7368 21.0785C27.7093 21.0785 27.6909 21.0785 27.6634 21.0785Z" fill="#6244BB"/>
					<path d="M23.6576 28.7511C22.5301 29.2919 21.3201 29.5669 20.0551 29.5669C18.7351 29.5669 17.4793 29.2736 16.3059 28.6777C15.9759 28.5219 15.8476 28.1186 16.0126 27.7886C16.1684 27.4586 16.5718 27.3302 16.9018 27.4861C17.4793 27.7794 18.0934 27.9811 18.7168 28.1002C19.5601 28.2652 20.4218 28.2744 21.2651 28.1277C21.8884 28.0177 22.5026 27.8252 23.0709 27.5502C23.4101 27.3944 23.8134 27.5227 23.9601 27.8619C24.1251 28.1919 23.9968 28.5952 23.6576 28.7511Z" fill="#6244BB"/>
					<path d="M12.4284 20.715C12.4101 20.715 12.3826 20.715 12.3642 20.715C11.9976 20.6784 11.7317 20.3484 11.7684 19.9817C12.0159 17.5892 13.2809 15.4258 15.2517 14.0325C15.5451 13.8217 15.9667 13.895 16.1776 14.1883C16.3884 14.4908 16.3151 14.9033 16.0217 15.1142C14.3717 16.2875 13.2992 18.1025 13.0976 20.11C13.0609 20.4584 12.7676 20.715 12.4284 20.715Z" fill="#6244BB"/>
					<path d="M20.0458 10.8425C18.625 10.8425 17.4608 11.9975 17.4608 13.4275C17.4608 14.8575 18.6158 16.0125 20.0458 16.0125C21.4758 16.0125 22.6308 14.8575 22.6308 13.4275C22.6308 11.9975 21.4758 10.8425 20.0458 10.8425Z" fill="#6244BB"/>
					<path d="M13.6292 21.7142C12.2083 21.7142 11.0442 22.8692 11.0442 24.2992C11.0442 25.7292 12.1992 26.8842 13.6292 26.8842C15.0592 26.8842 16.2142 25.7292 16.2142 24.2992C16.2142 22.8692 15.05 21.7142 13.6292 21.7142Z" fill="#6244BB"/>
					<path d="M26.3708 21.7142C24.9499 21.7142 23.7858 22.8692 23.7858 24.2992C23.7858 25.7292 24.9408 26.8842 26.3708 26.8842C27.8008 26.8842 28.9558 25.7292 28.9558 24.2992C28.9558 22.8692 27.8008 21.7142 26.3708 21.7142Z" fill="#6244BB"/>
				</svg>
				{{-- blade-formatter-enable --}}
                {{ __('Upgrade') }}
            </h2>
        </x-slot:head>

        <p class="mb-3">
            @if (Auth::user()->activePlan() != null)
                {{ __('You have currently') }}
                <strong class="text-heading-foreground">
                    {{ getSubscriptionName() }}
                </strong>
                {{ __('plan.') }}
                {{ __('Will refill automatically in') }} {{ getSubscriptionDaysLeft() }} {{ __('Days.') }}
                {{ checkIfTrial() == true ? __('You are in Trial time.') : '' }}
            @else
                {{ __('You have no subscription at the moment. Please select a subscription plan or a token pack.') }}
            @endif
        </p>

        <div class="relative mb-8">
            <h3 class="absolute left-1/2 top-[calc(50%-5px)] m-0 -translate-x-1/2 text-center text-xs font-normal">
                <strong class="text-[2em] font-semibold leading-none max-md:text-[1.5em]">
                    @if (Auth::user()->remaining_words == -1)
                        {{ __('Unlimited') }}
                    @else
                        {{ number_format((int) Auth::user()->remaining_words) }}
                    @endif
                </strong>
                <br>
                {{ __('Palavras') }}
            </h3>
            <div
                class="relative [&_.apexcharts-legend-text]:!m-0 [&_.apexcharts-legend-text]:!pe-2 [&_.apexcharts-legend-text]:ps-2 [&_.apexcharts-legend-text]:!text-foreground"
                id="chart-credit"
            >
            </div>
        </div>

        <p class="mb-4 text-center">
            <a
                class="text-heading-foreground underline px-8 py-4 text-xl border border-heading-foreground rounded-md inline-block"
                href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.payment.subscription')) }}"
            >
                @lang('Faça um upgrade no seu plano')
            </a>
            <span class="block mt-2">
                @lang('para gerar mais conteúdo.')
            </span>
        </p>

        <div class="flex w-full flex-wrap items-center justify-center gap-4 text-center">
            @if (getSubscriptionStatus())
                <x-button
                    variant="danger"
                    onclick="return confirm('Are you sure to cancel your plan? You will lose your remaining usage.');"
                    href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.payment.cancelActiveSubscription')) }}"
                >
                    <x-tabler-circle-minus class="size-4" />
                    {{ __('Cancel My Plan') }}
                </x-button>
            @endif
        </div>
    </x-card>
@endif

@push('script')
    <script src="{{ custom_theme_url('/assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script>
        // @formatter:off
        document.addEventListener("DOMContentLoaded", function() {
            "use strict";

            const options = {
                series: [{{ (int) Auth::user()->remaining_words }}, {{ (int) $total_words }}],
                labels: [@json(__('Remaining')), @json(__('Used'))],
                colors: ['hsl(var(--primary))', 'hsl(var(--primary) / 20%)'],
                tooltip: {
                    style: {
                        color: '#ffffff',
                    },
                },
                chart: {
                    type: 'donut',
                    height: 239,
                },
                legend: {
                    position: 'bottom',
                    fontFamily: 'inherit',
                },
                plotOptions: {
                    pie: {
                        startAngle: -90,
                        endAngle: 90,
                        offsetY: 0,
                        donut: {
                            size: '82%',
                        }
                    },
                },
                grid: {
                    padding: {
                        bottom: -130
                    }
                },
                stroke: {
                    width: 4,
                    colors: 'hsl(var(--background))'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 280,
                            height: 250
                        },
                    }
                }],
                dataLabels: {
                    enabled: false,
                }
            };
            window.ApexCharts && (new ApexCharts(document.getElementById('chart-credit'), options)).render();
        });
        // @formatter:on
    </script>
@endpush
