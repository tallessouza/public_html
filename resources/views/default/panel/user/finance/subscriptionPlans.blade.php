@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Plans'))
@section('titlebar_actions', '')
@section('titlebar_actions_before')
    <div class="mt-4 flex w-full">
        <x-remaining-credit
            class="text-2xs lg:ms-auto"
            legend-size="sm"
            style="inline"
            progress-height="sm"
        />
    </div>
@endsection

@inject('paymentControls', 'App\Http\Controllers\Finance\PaymentProcessController')
@inject('gatewayControls', 'App\Http\Controllers\Finance\GatewayController')

@section('content')
    <div class="py-10">
        <div class="flex flex-col gap-10">
            <div class="w-full">
                @include('panel.user.finance.subscriptionStatus')
            </div>
            <div class="w-full">
                @if ($plans->count() > 0)
                    <h2 class="mb-5">
                        {{ __('Subscription Plans') }}:
                    </h2>
                @endif
                <div class="grid grid-cols-4 gap-3 max-lg:grid-cols-2 max-md:grid-cols-1">
                    @foreach ($plans as $plan)
                        <div @class([
                            'w-full rounded-3xl border bg-background',
                            'shadow-[0_7px_20px_rgba(0,0,0,0.04)]' => $plan->is_featured,
                        ])>
                            <div class="flex h-full flex-col p-7">
                                <div class="mb-2 flex items-start text-[50px] font-bold leading-none text-heading-foreground">
                                    @if (currencyShouldDisplayOnRight(currency()->symbol))
                                        {{ $plan->price }} <small class='inline-flex text-[0.35em] font-normal'>
                                            {{ currency()->symbol }}
                                        </small>
                                    @else
                                        <small class='inline-flex text-[0.35em] font-normal'>
                                            {{ currency()->symbol }}
                                        </small>
                                        {{ $plan->price }}
                                    @endif
                                    <div class="ms-2 mt-2 inline-flex flex-col items-start gap-2 text-[0.3em]">
                                        {{ __(formatCamelCase($plan->frequency)) }}
                                        @if ($plan->is_featured == 1)
                                            <div class="inline-flex rounded-full bg-gradient-to-r from-[#ece7f7] via-[#e7c5e6] to-[#e7ebf9] px-3 py-1 text-3xs text-black">
                                                {{ __('Popular plan') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <p class="text-sm font-medium leading-none opacity-50">
                                    {{ __($plan->name) }}
                                </p>

                                <ul class="my-6 text-sm text-heading-foreground">
                                    @if ($plan->trial_days != 0)
                                        <li class="mb-3">
                                            <span class="size-5 me-1 inline-flex items-center justify-center rounded-xl bg-primary/10 align-middle text-primary">
                                                <x-tabler-check class="size-3.5" />
                                            </span>
                                            {{ number_format($plan->trial_days) . ' ' . __('Days of free trial.') }}
                                        </li>
                                    @endif
                                    <li class="mb-3">
                                        <span class="size-5 me-1 inline-flex items-center justify-center rounded-xl bg-primary/10 align-middle text-primary">
                                            <x-tabler-check class="size-3.5" />
                                        </span>
                                        {{ __('Access') }}
                                        <strong>{{ __($plan->checkOpenAiItemCount()) }}</strong> {{ __('Templates') }}
                                        <div class="group relative inline-block before:absolute before:-inset-2.5">
                                            <span class="peer relative -mt-6 inline-flex !h-6 !w-6 cursor-pointer items-center justify-center">
                                                <x-tabler-info-circle-filled class="size-4 opacity-20" />
                                            </span>
                                            <div
                                                class="min-w-60 pointer-events-none invisible absolute start-full top-1/2 z-10 ms-2 max-h-96 -translate-y-1/2 translate-x-2 scale-105 overflow-y-auto rounded-lg border bg-background p-5 opacity-0 shadow-xl transition-all before:absolute before:-start-2 before:top-0 before:h-full before:w-2 group-hover:pointer-events-auto group-hover:visible group-hover:translate-x-0 group-hover:opacity-100 [&.anchor-end]:end-2 [&.anchor-end]:start-auto [&.anchor-end]:me-2 [&.anchor-end]:ms-0"
                                                data-set-anchor="true"
                                            >
                                                <ul>
                                                    @foreach ($openAiList->groupBy('filters') as $key => $openAi)
                                                        <li class="mb-3 mt-5 first:mt-0">
                                                            <h5 class="text-base">
                                                                {{ ucfirst($key) }}
                                                            </h5>
                                                        </li>
                                                        @php($openAi = \App\Helpers\Classes\Helper::sortingOpenAiSelected($openAi, $plan->open_ai_items))

                                                        @foreach ($openAi as $itemOpenAi)
                                                            <li class="mb-1.5 flex items-center gap-1.5 text-heading-foreground">
                                                                <span @class([
                                                                    'bg-primary/10 text-primary' => $plan->checkOpenAiItem($itemOpenAi->slug),
                                                                    'bg-foreground/10 text-foreground' => !$plan->checkOpenAiItem(
                                                                        $itemOpenAi->slug),
                                                                    'size-4 inline-flex items-center justify-center rounded-xl align-middle',
                                                                ])>
                                                                    @if ($plan->checkOpenAiItem($itemOpenAi->slug))
                                                                        <x-tabler-check class="size-3" />
                                                                    @else
                                                                        <x-tabler-minus class="size-3" />
                                                                    @endif
                                                                </span>
                                                                <small @class(['opacity-60' => !$plan->checkOpenAiItem($itemOpenAi->slug)])>
                                                                    {{ $itemOpenAi->title }}
                                                                </small>
                                                            </li>
                                                        @endforeach
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                    @foreach (explode(',', $plan->features) as $item)
                                        <li class="mb-3">
                                            <span class="size-5 me-1 inline-flex items-center justify-center rounded-xl bg-primary/10 align-middle text-primary">
                                                <x-tabler-check class="size-3.5" />
                                            </span>
                                            {{ $item }}
                                        </li>
                                    @endforeach
                                    @if ($plan->is_team_plan)
                                        <li class="mb-3">
                                            <span class="size-5 me-1 inline-flex items-center justify-center rounded-xl bg-primary/10 align-middle text-primary">
                                                <x-tabler-check class="size-3.5" />
                                            </span>
                                            <strong>
                                                {{ number_format($plan->plan_allow_seat) }}
                                            </strong>
                                            {{ __('Team allow seats') }}
                                        </li>
                                    @endif
                                    @if ($plan->display_word_count)
                                        <li class="mb-3">
                                            <span class="size-5 me-1 inline-flex items-center justify-center rounded-xl bg-primary/10 align-middle text-primary">
                                                <x-tabler-check class="size-3.5" />
                                            </span>
                                            @if ((int) $plan->total_words >= 0)
                                                <strong>
                                                    {{ number_format($plan->total_words) }}
                                                </strong>
                                                {{ __('Word Tokens') }}
                                            @else
                                                <strong>{{ __('Unlimited') }}</strong> {{ __('Word Tokens') }}
                                            @endif
                                        </li>
                                    @endif
                                    @if ($plan->display_imag_count)
                                        <li class="mb-3">
                                            <span class="size-5 me-1 inline-flex items-center justify-center rounded-xl bg-primary/10 align-middle text-primary">
                                                <x-tabler-check class="size-3.5" />
                                            </span>
                                            @if ((int) $plan->total_images >= 0)
                                                <strong>
                                                    {{ number_format($plan->total_images) }}
                                                </strong>
                                                {{ __('Image Tokens') }}
                                            @else
                                                <strong>
                                                    {{ __('Unlimited') }}
                                                </strong>
                                                {{ __('Image Tokens') }}
                                            @endif
                                        </li>
                                    @endif

                                </ul>

                                @if ($activesubid == $plan->id)
                                    <div class="mt-auto text-center">
                                        <div class="flex flex-col gap-2">
                                            <span class="text-green-500">
                                                <b>{{ __('Already Subscribed') }}</b>
                                            </span>
                                            <a
                                                class="text-foreground/60"
                                                onclick="return confirm('Are you sure to cancel your plan? You will lose your remaining usage.');"
                                                href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.payment.cancelActiveSubscription')) }}"
                                            >
                                                {{ __('Cancel Subscription') }}
                                            </a>
                                        </div>
                                    </div>
                                @elseif($activesubid != null)
                                    <div class="mt-auto text-center">
                                        <div class="flex flex-col gap-2">
                                            <span class="text-foreground/60">
                                                <b>{{ __('You have an active subscription.') }}</b>
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-auto text-center">
                                        @if ($is_active_gateway == 1)
                                            @php($planid = $plan->id)
                                            @if ($plan->price == 0)
                                                <x-button
                                                    class="w-full"
                                                    href="{{ $app_is_demo ? '#' : LaravelLocalization::localizeUrl(route('dashboard.user.payment.startSubscriptionProcess', ['planId' => $planid, 'gatewayCode' => 'freeservice'])) }}"
                                                    onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
                                                    variant="ghost-shadow"
                                                >
                                                    {{ __('Choose plan') }}
                                                </x-button>
                                            @else
                                                <x-modal
                                                    title="{{ __('Continue with') }}"
                                                    disable-modal="{{ $app_is_demo }}"
                                                    disable-modal-message="{{ __('This feature is disabled in Demo version.') }}"
                                                >
                                                    <x-slot:trigger
                                                        class="w-full"
                                                        variant="ghost-shadow"
                                                    >
                                                        {{ __('Choose plan') }}
                                                    </x-slot:trigger>
                                                    <x-slot:modal>
                                                        <div class="flex flex-col gap-4">
                                                            @foreach ($activeGateways as $gateway)
                                                                @if ($gateway->code == 'revenuecat')
                                                                    @continue
                                                                @endif
                                                                @php($data = $gatewayControls->gatewayData($gateway->code))
                                                                <x-button
                                                                    class="w-full"
                                                                    hover-variant="secondary"
                                                                    href="{{ $app_is_demo ? '#' : LaravelLocalization::localizeUrl(route('dashboard.user.payment.startSubscriptionProcess', ['planId' => $planid, 'gatewayCode' => $data['code']])) }}"
                                                                    onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
                                                                    variant="ghost-shadow"
                                                                >
                                                                    <div class="m-0 flex h-9 w-full items-center justify-between align-middle">
                                                                        @if ($data['whiteLogo'] == 1)
                                                                            <img
                                                                                class="rounded-3xl bg-primary px-3"
                                                                                src="{{ custom_theme_url($data['img']) }}"
                                                                                style="max-height:24px;"
                                                                                alt="{{ $data['title'] }}"
                                                                            />
                                                                        @else
                                                                            <img
                                                                                class="rounded-3xl px-3"
                                                                                src="{{ custom_theme_url($data['img']) }}"
                                                                                style="max-height:24px;"
                                                                                alt="{{ $data['title'] }}"
                                                                            />
                                                                        @endif
                                                                        {{ $data['title'] }}
                                                                    </div>
                                                                </x-button>
                                                            @endforeach
                                                        </div>
                                                    </x-slot:modal>
                                                </x-modal>
                                            @endif
                                        @else
                                            <p>{{ __('Please enable a payment gateway') }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="w-full">
                @if ($prepaidplans->count() > 0)
                    <h2 class="mb-5">
                        {{ __('Token Packs') }}:
                    </h2>
                @endif
                <div class="grid grid-cols-4 gap-3 max-lg:grid-cols-2 max-md:grid-cols-1">
                    @foreach ($prepaidplans as $plan)
                        <div @class([
                            'w-full rounded-3xl border bg-background',
                            'shadow-[0_7px_20px_rgba(0,0,0,0.04)]' => $plan->is_featured,
                        ])>
                            <div class="flex h-full flex-col p-7">
                                <div class="mb-2 flex items-start text-[50px] font-bold leading-none text-heading-foreground">
                                    @if (currencyShouldDisplayOnRight(currency()->symbol))
                                        {{ $plan->price }}
                                        <small class='inline-flex text-[0.35em] font-normal'>
                                            {{ currency()->symbol }}
                                        </small>
                                    @else
                                        <small class='inline-flex text-[0.35em] font-normal'>
                                            {{ currency()->symbol }}
                                        </small>
                                        {{ $plan->price }}
                                    @endif
                                    <div class="ms-2 mt-2 inline-flex flex-col items-start gap-2 text-[0.3em]">
                                        {{ __('One time') }}
                                        @if ($plan->is_featured == 1)
                                            <div
                                                class="inline-flex rounded-full bg-gradient-to-r from-[#ece7f7] via-[#e7c5e6] to-[#e7ebf9] px-[0.75rem] py-[0.25rem] text-3xs text-black">
                                                {{ __('Popular pack') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-sm font-medium leading-none opacity-60">
                                    {{ __($plan->name) }}
                                </p>
                                <ul class="my-6 list-none p-0 text-sm text-heading-foreground">
                                    <li class="mb-3">
                                        <span class="size-5 me-1 inline-flex items-center justify-center rounded-xl bg-primary/10 align-middle text-primary">
                                            <x-tabler-check class="size-3.5" />
                                        </span>
                                        {{ __('Access') }}
                                        <strong>
                                            {{ __($plan->checkOpenAiItemCount()) }}
                                        </strong>
                                        {{ __('Templates') }}
                                        <div class="group relative inline-block before:absolute before:-inset-2.5">
                                            <span class="peer relative -mt-6 inline-flex !h-6 !w-6 cursor-pointer items-center justify-center">
                                                <x-tabler-info-circle-filled class="size-4 opacity-20" />
                                            </span>
                                            <div
                                                class="min-w-60 pointer-events-none invisible absolute start-full top-1/2 z-10 ms-2 max-h-96 -translate-y-1/2 translate-x-2 scale-105 overflow-y-auto rounded-lg border bg-background p-5 opacity-0 shadow-xl transition-all before:absolute before:-start-2 before:top-0 before:h-full before:w-2 group-hover:pointer-events-auto group-hover:visible group-hover:translate-x-0 group-hover:opacity-100 [&.anchor-end]:end-2 [&.anchor-end]:start-auto [&.anchor-end]:me-2 [&.anchor-end]:ms-0"
                                                data-set-anchor="true"
                                            >
                                                <ul>
                                                    @foreach ($openAiList->groupBy('filters') as $key => $openAi)
                                                        <li class="mb-3 mt-5 first:mt-0">
                                                            <h5 class="text-base">{{ ucfirst($key) }}</h5>
                                                        </li>
                                                        @php($openAi = \App\Helpers\Classes\Helper::sortingOpenAiSelected($openAi, $plan->open_ai_items))
                                                        @foreach ($openAi as $itemOpenAi)
                                                            <li class="mb-1.5 flex items-center gap-1.5 text-heading-foreground">
                                                                <span @class([
                                                                    'bg-primary/10 text-primary' => $plan->checkOpenAiItem($itemOpenAi->slug),
                                                                    'bg-foreground/10 text-foreground' => !$plan->checkOpenAiItem(
                                                                        $itemOpenAi->slug),
                                                                    'size-4 inline-flex items-center justify-center rounded-xl align-middle',
                                                                ])>
                                                                    @if ($plan->checkOpenAiItem($itemOpenAi->slug))
                                                                        <x-tabler-check class="size-3" />
                                                                    @else
                                                                        <x-tabler-minus class="size-3" />
                                                                    @endif
                                                                </span>
                                                                <small @class(['opacity-60' => !$plan->checkOpenAiItem($itemOpenAi->slug)])>
                                                                    {{ $itemOpenAi->title }}
                                                                </small>
                                                            </li>
                                                        @endforeach
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                    @foreach (explode(',', $plan->features) as $item)
                                        <li class="mb-3">
                                            <span class="size-5 me-1 inline-flex items-center justify-center rounded-xl bg-primary/10 align-middle text-primary">
                                                <x-tabler-check class="size-3.5" />
                                            </span>
                                            {{ $item }}
                                        </li>
                                    @endforeach
                                    @if ($plan->display_word_count)
                                        <li class="mb-3">
                                            <span class="size-5 me-1 inline-flex items-center justify-center rounded-xl bg-primary/10 align-middle text-primary">
                                                <x-tabler-check class="size-3.5" />
                                            </span>
                                            @if ((int) $plan->total_words >= 0)
                                                <strong>
                                                    {{ number_format($plan->total_words) }}</strong>
                                                {{ __('Word Tokens') }}
                                            @else
                                                <strong>
                                                    {{ __('Unlimited') }}
                                                </strong>
                                                {{ __('Word Tokens') }}
                                            @endif
                                        </li>
                                    @endif
                                    @if ($plan->display_imag_count)
                                        <li class="mb-3">
                                            <span class="size-5 me-1 inline-flex items-center justify-center rounded-xl bg-primary/10 align-middle text-primary">
                                                <x-tabler-check class="size-3.5" />
                                            </span>
                                            @if ((int) $plan->total_images >= 0)
                                                <strong>
                                                    {{ number_format($plan->total_images) }}</strong>
                                                {{ __('Image Tokens') }}
                                            @else
                                                <strong>
                                                    {{ __('Unlimited') }}
                                                </strong>
                                                {{ __('Image Tokens') }}
                                            @endif
                                        </li>
                                    @endif
                                </ul>
                                <div class="mt-auto text-center">
                                    @if ($is_active_gateway == 1)
                                        @php($planid = $plan->id)
                                        @if ($plan->price == 0)
                                            <x-button
                                                class="w-full"
                                                href="{{ $app_is_demo ? '#' : LaravelLocalization::localizeUrl(route('dashboard.user.payment.startPrepaidPaymentProcess', ['planId' => $planid, 'gatewayCode' => 'freeservice'])) }}"
                                                onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
                                                variant="ghost-shadow"
                                            >
                                                {{ __('Choose pack') }}
                                            </x-button>
                                        @else
                                            <x-modal
                                                title="{{ __('Continue with') }}"
                                                disable-modal="{{ $app_is_demo }}"
                                                disable-modal-message="{{ __('This feature is disabled in Demo version.') }}"
                                            >
                                                <x-slot:trigger
                                                    class="w-full"
                                                    variant="ghost-shadow"
                                                >
                                                    {{ __('Choose pack') }}
                                                </x-slot:trigger>
                                                <x-slot:modal>
                                                    <div class="flex flex-col gap-4">
                                                        @foreach ($activeGateways as $gateway)
                                                            @if ($gateway->code == 'revenuecat')
                                                                @continue
                                                            @endif
                                                            @php($data = $gatewayControls->gatewayData($gateway->code))
                                                            <x-button
                                                                class="w-full"
                                                                hover-variant="secondary"
                                                                href="{{ $app_is_demo ? '#' : LaravelLocalization::localizeUrl(route('dashboard.user.payment.startPrepaidPaymentProcess', ['planId' => $planid, 'gatewayCode' => $data['code']])) }}"
                                                                onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
                                                                variant="ghost-shadow"
                                                            >
                                                                <div class="flex h-9 w-full items-center justify-between align-middle">
                                                                    @if ($data['whiteLogo'] == 1)
                                                                        <img
                                                                            class="rounded-3xl bg-primary px-3"
                                                                            src="{{ custom_theme_url($data['img']) }}"
                                                                            style="max-height:24px;"
                                                                            alt="{{ $data['title'] }}"
                                                                        />
                                                                    @else
                                                                        <img
                                                                            class="rounded-3xl px-3"
                                                                            src="{{ custom_theme_url($data['img']) }}"
                                                                            style="max-height:24px;"
                                                                            alt="{{ $data['title'] }}"
                                                                        />
                                                                    @endif
                                                                    {{ $data['title'] }}
                                                                </div>
                                                            </x-button>
                                                        @endforeach
                                                    </div>
                                                </x-slot:modal>
                                            </x-modal>
                                        @endif
                                    @else
                                        <p>
                                            {{ __('Please enable a payment gateway') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        $('[data-click="tooltip"]').on('click', function() {

            let id = $(this).data('id');

            if ($('#' + id).hasClass('d-none')) {
                $('#' + id).removeClass('d-none');
            } else {
                $('#' + id).addClass('d-none');
            }

        })

        $(document).ready(function() {
            let plan = '{{ request('plan') }}';

            if (plan) {
                $('#gatewayModal_' + plan).modal('show');
            }
        });
    </script>
@endpush
