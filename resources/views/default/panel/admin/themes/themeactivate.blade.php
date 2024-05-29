@extends('panel.layout.app')
@section('title', 'Marketplace Payment')
@section('titlebar_actions', '')

@section('content')
    <div class="py-10">
        <div class="mx-auto w-full text-center lg:w-6/12 lg:px-9">
            <h2 class="mb-4">
                {{ $success ? __('Payment Success') : __('Payment Failed') }}
            </h2>

            <div class="mx-auto mb-4 rounded-lg border text-heading-foreground">
                <div class="grid grid-cols-2 items-center gap-2 border-b p-4">
                    <p class="mb-0 text-start">
                        {{ __('Add-on') }}
                    </p>
                    <p class="mb-0 text-end">
                        {{ $theme->name }}
                    </p>
                </div>
                <div class="grid grid-cols-2 items-center gap-2 p-4">
                    <p class="mb-0 text-start">
                        {{ __('Total') }}:
                    </p>
                    <p class="mb-0 text-end text-xl font-bold">
                        @if (currencyShouldDisplayOnRight(currency()->symbol))
                            {{ $theme->price }}{{ currency()->symbol }}
                        @else
                            {{ currency()->symbol }}{{ $theme->price }}
                        @endif
                    </p>
                </div>
            </div>
            @if ($success)
                <x-alert variant="info">
                    @lang('Your payment is success, you can install your theme.')
                </x-alert>
                @php
                    if ($theme->slug == 'default') {
                        $is_active = setting('front_theme') == 'default' && setting('dash_theme') == 'default';
                    } else {
                        $is_active = setting('front_theme') == $theme->slug || setting('dash_theme') == $theme->slug;
                    }
                @endphp
                <x-button
                    class="mt-4 w-full"
                    size="lg"
                    :disabled="$is_active"
                    variant="{{ $theme->price == 0 ? 'success' : 'primary' }}"
                    size="lg"
                    href="{{ route('dashboard.admin.themes.activate', ['slug' => $theme->slug]) }}"
                >
                    {{ $is_active ? 'Activated' : 'Activate' }}
                </x-button>
            @else
                <x-alert variant="danger">
                    @lang('Your payment is unsuccessful')
                </x-alert>

                @if ($app_is_demo)
                    <x-button
                        class="w-full"
                        size="lg"
                        href="#"
                        onclick="return toastr.info('This feature is disabled in Demo version.')"
                        disabled
                    >
                        {{ __('Buy Now') }}
                    </x-button>
                @else
                    @php
                        $is_license = $theme->licensed == 1;
                    @endphp
                    <x-button
                        class="mt-4 w-full"
                        :disabled="$is_license"
                        size="lg"
                        href="{{ route('dashboard.admin.marketplace.buyextesion', ['slug' => $theme->slug]) }}"
                    >
                        {{ __('Buy Now') }}
                    </x-button>
                @endif
            @endif
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('https://js.stripe.com/v3/') }}"></script>

    <script src="{{ custom_theme_url('/assets/js/panel/marketplace.js') }}"></script>
@endpush
