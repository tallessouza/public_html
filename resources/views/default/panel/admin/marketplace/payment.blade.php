@extends('panel.layout.app')
@section('title', 'Payment')
@section('titlebar_actions', '')

@section('content')
    <div class="py-10">
        <div class="mx-auto w-full text-center lg:w-6/12 lg:px-9">
            <h2 class="mb-4">
                {{ __('Select a payment method') }}
            </h2>

            <p class="mx-auto mb-8 lg:w-10/12">
                {{ __('How do you want to pay? Select a payment method to confirm your order') }}.
            </p>

            <div class="mx-auto mb-4 rounded-lg border text-heading-foreground">
                <div class="grid grid-cols-2 items-center gap-2 border-b p-4">
                    <p class="mb-0 text-start">
                        {{ $item['is_theme'] ? __('Theme') : __('Add-on') }}
                    </p>
                    <p class="mb-0 text-end">
                        {{ $item['name'] }}
                    </p>
                </div>
                <div class="grid grid-cols-2 items-center gap-2 p-4">
                    <p class="mb-0 text-start">
                        {{ __('Total') }}:
                    </p>
                    <p class="mb-0 text-end text-xl font-bold">
                        @if (currencyShouldDisplayOnRight('USD'))
                            {{ $item['price'] }} USD
                        @else
                            {{ 'USD' }} {{ $item['price'] }}
                        @endif
                    </p>
                </div>
            </div>
            <p class="mb-7 opacity-60">
                {{ __('Tax included. Your payment is secured vis SSL.') }}
            </p>

            <div id="checkout">

            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/marketplace.js') }}"></script>
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        // This is your test publishable API key.
        const stripe = Stripe("{{ $data['stripeKey'] }}");

        initialize();

        // Create a Checkout Session as soon as the page loads
        async function initialize() {

            const clientSecret ='{{ $data['stripe']['client_secret'] }}';

            const checkout = await stripe.initEmbeddedCheckout({
                clientSecret,
            });

            // Mount Checkout
            checkout.mount('#checkout');
        }
    </script>
@endpush
