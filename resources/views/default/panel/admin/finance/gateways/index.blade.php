@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Payment Gateways'))
@section('titlebar_actions', '')

@section('content')
    <div class="py-10">
        @if ($settings_two->liquid_license_type != 'Extended License')
            <div class="mb-4 w-full rounded-xl bg-red-100 !p-3 text-center text-red-600 dark:bg-orange-600/20 dark:text-orange-200">
                {{ __('To access this page, you should upgrade to Extended License.') }} <a href="{{ route('LaravelInstaller::license.upgrade') }}"><u> {{ __('Upgrade License') }}</u></a>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                @foreach ($gateways as $entry)
                    <x-card
                        class="flex min-h-[250px] w-full flex-col rounded-md px-0 pt-0 text-center"
                        class:body="flex flex-col items-center"
                        size="sm"
                    >
                        <div @class([
                            'flex aspect-square w-full justify-center rounded-md mb-2',
                            'bg-[#1a1d23]' => $entry['whiteLogo'] == 1,
                        ])>
                            <img
                                class="max-w-32 h-full max-h-32 w-full object-contain object-center"
                                src="{{ url('') . custom_theme_url($entry['img']) }}"
                                alt="{{ $entry['title'] }}"
                            />
                        </div>
                        <h3 class="mb-4 w-full [word-break:break-word]">
                            <a
                                href="{{ $entry['link'] }}"
                                target="_blank"
                            >
                                {{ $entry['title'] }}
                            </a>
                        </h3>
                        <div class="justify-content-center mt-auto flex w-full">
                            @if ($entry['available'] == 1)
                                <x-button
                                    class="w-full"
                                    href="{{ route('dashboard.admin.finance.paymentGateways.settings', $entry['code']) }}"
                                >
                                    {{ __('Settings') }}
                                </x-button>
                            @else
                                <h6 class="italic opacity-60">
                                    {{ __('Coming soon') }}
                                </h6>
                            @endif
                        </div>
                        @if ($entry['available'] == 1)
                            <div @class([
                                'rounded-full absolute start-2 top-2 px-2 py-1 text-white inline-flex items-center gap-1 font-medium',
                                'bg-green-500' => $entry['active'] == 1,
                                'bg-red-500' => $entry['active'] == 0,
                            ])>
                                @if ($entry['active'] == 1)
                                    <x-tabler-check class="size-4" />
                                @else
                                    <x-tabler-x class="size-4" />
                                @endif
                                {{ $entry['active'] == 1 ? __('Active') : __('Inactive') }}
                            </div>
                        @endif
                    </x-card>
                @endforeach
            </div>
        @endif
    </div>
@endsection
