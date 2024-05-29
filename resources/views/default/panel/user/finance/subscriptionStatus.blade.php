@php
    $team = Auth::user()->getAttribute('team');
    $teamManager = Auth::user()->getAttribute('teamManager');
@endphp

@if ($team)
    <x-card
        class="max-md:text-center"
        szie="lg"
    >
        <div class="flex flex-wrap items-center justify-between gap-y-4 text-base font-medium leading-normal">
            <div class="lg-w/5-12 w-full md:w-1/2">
                <h2 class="mb-[1em]">{{ __('Active Workspace:') }}</h2>
                <p class="mb-4 font-bold">
                    {{ $teamManager->name . ' ' . $teamManager->surname }}
                    <x-badge class="ms-2 text-2xs">
                        @lang('Team Manager')
                    </x-badge>
                </p>

                @lang("You have the Team plan which has a remaining balance of <strong class='font-bold '>:word</strong> words and <strong class='font-bold '>:image</strong> images. You can contact your team manager if you need more credits.", ['word' => Auth::user()->remaining_words, 'image' => Auth::user()->remaining_images])
            </div>
            <div class="ms-auto w-full md:w-1/2">
                <div class="relative">
                    <h3 class="absolute start-1/2 top-[calc(50%-5px)] m-0 -translate-x-1/2 text-center text-xs font-normal">
                        <strong class="text-[2em] font-semibold leading-none max-sm:text-[1.5em]">
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
        </div>
    </x-card>
@else
    <x-card
        class:body="flex flex-wrap justify-between"
        size="lg"
    >
        <div class="mb-4 md:mb-0 lg:w-2/5">
            <h3 class="mb-7">{{ __('Upgrade') }}</h3>

            <p class="mb-3">
                @if (Auth::user()->activePlan() != null)
                    {{ __('You have currently') }}
                    <strong class="text-heading-foreground">{{ getSubscriptionName() }}</strong>
                    {{ __('plan.') }}
                    {{ __('Will refill automatically in') }} {{ getSubscriptionDaysLeft() }} {{ __('Days.') }}
                    {{ checkIfTrial() == true ? __('You are in Trial time.') : '' }}
                @else
                    {{ __('You have no subscription at the moment. Please select a subscription plan or a token pack.') }}
                @endif
            </p>

            <p class="mb-4">
                @if ($setting->feature_ai_image)
                    {{ __('Total') }}
                    <strong class="text-heading-foreground">
                        @if (Auth::user()->remaining_words == -1)
                            {{ __('Unlimited') }}
                        @else
                            {{ number_format((int) Auth::user()->remaining_words) }}
                        @endif
                    </strong>
                    {{ __('word and') }}
                    <strong class="text-heading-foreground">
                        @if (Auth::user()->remaining_images == -1)
                            {{ __('Unlimited') }}
                        @else
                            {{ number_format((int) Auth::user()->remaining_images) }}
                        @endif
                    </strong>
                    {{ __('image tokens left.') }}
                @else
                    {{ __('Total') }}
                    <strong class="text-heading-foreground">
                        @if (Auth::user()->remaining_words == -1)
                            {{ __('Unlimited') }}
                        @else
                            {{ number_format((int) Auth::user()->remaining_words) }}
                        @endif
                    </strong>
                    {{ __('tokens left.') }}
                @endif
            </p>

            <div class="flex flex-wrap items-center gap-4">
                <x-button
                    class="hover:bg-primary"
                    variant="ghost-shadow"
                    href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.payment.subscription')) }}"
                >
                    <x-tabler-plus class="size-4" />
                    {{ __('Select a Plan') }}
                </x-button>

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
        </div>

        <div class="ms-auto w-full lg:w-1/2">
            <div class="relative">
                <h3 class="absolute left-1/2 top-[calc(50%-5px)] m-0 -translate-x-1/2 text-center text-xs font-normal">
                    <strong class="text-[2em] font-semibold leading-none max-sm:text-[1.5em]">
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
                colors: ['#9A34CD', 'rgba(154,52,205,0.2)'],
                tooltip: {
                    style: {
                        color: '#ffffff',
                    },
                },
                chart: {
                    type: 'donut',
                    height: 205,
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
                            size: '75%',
                        }
                    },
                },
                grid: {
                    padding: {
                        bottom: -130
                    }
                },
                stroke: {
                    width: 5,
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
