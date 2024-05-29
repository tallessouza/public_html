@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Marketplace'))
@section('titlebar_actions')
    <div class="flex flex-wrap gap-2">
        <x-button
            variant="ghost-shadow"
            href="{{ route('dashboard.admin.marketplace.liextension') }}"
        >
            {{ __('Manage Addons') }}
        </x-button>
        <x-button href="{{ route('dashboard.admin.marketplace.index') }}">
            <x-tabler-plus class="size-4" />
            {{ __('Browse Add-ons') }}
        </x-button>
    </div>
@endsection

@section('content')
    <div class="py-10">
        <div class="flex flex-col gap-9">
            @include('panel.admin.market.components.marketplace-filter')
            <div class="lqd-extension-grid grid grid-cols-1 gap-7 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $item)
                    <x-card
                        class="lqd-extension relative flex flex-col rounded-[20px] transition-all hover:-translate-y-1 hover:shadow-lg"
                        class:body="flex flex-col"
                        data-price="{{ $item['price'] }}"
                        data-installed="{{ $item['installed'] }}"
                        data-name="{{ $item['name'] }}"
                    >
                        @if (trim($item['badge'], ' ') != '' || $item['price'] == 0)
                            <p class="absolute end-5 top-5 m-0 rounded bg-[#FFF1DB] px-2 py-1 text-4xs font-semibold uppercase leading-tight tracking-widest text-[#242425]">
                                @if (trim($item['badge'], ' ') != '')
                                    {{ $item['badge'] }}
                                @elseif ($item['price'] == 0)
                                    @lang('Free')
                                @endif
                            </p>
                        @endif

                            @if($item['version'] != $item['db_version']  && $item['installed'])
                                <p class="absolute end-5 top-{{ $item['price'] == 0 ? '10' : '5' }} m-0 rounded bg-red-500 px-2 py-1 text-4xs font-semibold uppercase leading-tight tracking-widest text-[#242425]">
                                    <a href="{{ route('dashboard.admin.marketplace.liextension') }}">Update Available</a>
                                </p>
                            @endif
                        <div class="size-[53px] mb-6 flex items-center rounded-xl">
                            <img
                                src="{{ $item['icon'] }}"
                                width="53"
                                height="53"
                                alt="{{ $item['name'] }}"
                            >
                            @if ($item['installed'])
                                <p class="mb-0 ms-3 flex items-center gap-2 text-2xs font-medium">
                                    <span class="size-2 inline-block rounded-full bg-green-500"></span>
                                    {{ __('Installed') }}
                                </p>
                            @endif
                        </div>

                        <div class="mb-7 flex flex-wrap items-center gap-2">
                            <h3 class="m-0 text-xl font-semibold">
                                {{ $item['name'] }}
                            </h3>
                            <p class="review m-0 flex items-center gap-1 text-sm font-medium text-heading-foreground">
                                <x-tabler-star-filled class="size-3" />
                                {{ number_format($item['review'], 1) }}
                            </p>
                        </div>
                        <p class="mb-7 text-base leading-normal">
                            {{ $item['description'] }}
                        </p>

                        <div class="mt-auto flex flex-wrap items-center gap-2">
                            @foreach ($item['categories'] as $tag)
                                {{ $tag }}
                                @if (!$loop->last)
                                    <span class="size-1 inline-block rounded-full bg-foreground/10"></span>
                                @endif
                            @endforeach
                        </div>
                        <a
                            class="absolute inset-0 z-1"
                            href="{{ route('dashboard.admin.marketplace.extension', ['slug' => $item['slug']]) }}"
                        >
                            <span class="sr-only">
                                {{ __('View details') }}
                            </span>
                        </a>
                    </x-card>
                @endforeach
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/marketplace.js') }}"></script>
@endpush
