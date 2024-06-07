@extends('panel.layout.app')
@section('title', __('Support Requests'))
@section('titlebar_actions')
@if (Auth::user()->type != 'admin')
<!-- <x-button href="{{ route('dashboard.support.new') }}">
            {{ __('Create New Support Request') }}
            <x-tabler-plus class="size-4" />
        </x-button> -->
@endif
@endsection
@section('content')
<div class="w-full">
    <x-card class="flex min-h-full flex-col text-xs" class:head="border-b-0 pt-8 pb-0 px-8" class:body="flex flex-wrap px-8 pt-5 pb-8 grow" size="lg">
        <x-slot:head class="flex items-center justify-between gap-2">
            <h2 class="m-0 flex items-center gap-4">
                {{-- blade-formatter-disable --}}
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="40" rx="8" fill="#6244BB" fill-opacity="0.12" />
                    <path d="M14.83 21.3522C13.1525 21.3522 11.7775 22.718 11.7775 24.4047C11.7775 26.0913 13.1434 27.4572 14.83 27.4572C16.5075 27.4572 17.8825 26.0913 17.8825 24.4047C17.8825 22.718 16.5075 21.3522 14.83 21.3522Z" fill="#6244BB" />
                    <path d="M24.2351 24.643C22.8143 24.643 21.6593 25.798 21.6593 27.2188C21.6593 28.6397 22.8143 29.7947 24.2351 29.7947C25.6559 29.7947 26.8109 28.6397 26.8109 27.2188C26.8109 25.798 25.6559 24.643 24.2351 24.643Z" fill="#6244BB" />
                    <path d="M23.2908 10.6042C20.5683 10.6042 18.3592 12.8134 18.3592 15.5359C18.3592 18.2584 20.5683 20.4675 23.2908 20.4675C26.0133 20.4675 28.2225 18.2584 28.2225 15.5359C28.2225 12.8134 26.0133 10.6042 23.2908 10.6042Z" fill="#6244BB" />
                </svg>
                {{-- blade-formatter-enable --}}
                {{ __('Suporte Lendário') }}
            </h2>
            <!-- <x-badge class="px-5 py-2.5 text-2xs text-foreground max-md:hidden">
                @lang('Valores dos Documentos')
            </x-badge> -->
        </x-slot:head>

        <div class="mb-5 lg:w-1/2">
            <p>
                Teve algum problema com a ferramenta? Contate o nosso suporte clicando no link abaixo.
            </p>
            <a class="text-heading-foreground underline" href="https://academialendaria.ai/suporte" target="blank"> 
                @lang('Suporte.')
            </a>
        </div>

        <!-- <div class="mb-6 flex w-full border-b pb-6 max-md:flex-col">
            <div class="grow basis-0 border-e px-9 !ps-0 transition-colors max-md:mb-3 max-md:w-full max-md:border-b max-md:border-e-0 max-md:px-0 max-md:pb-3">
                <p class="mb-4 font-medium text-foreground/70">
                    {{ __('Words Left') }}
                </p>
                <p class="text-3xl font-semibold leading-none text-heading-foreground">
                    @if (Auth::user()->remaining_words == -1)
                    @lang('Unlimited')
                    @else
                    {{ number_format((int) Auth::user()->remaining_words) }}
                    @endif
                </p>
            </div>
            @if ($setting->feature_ai_image)
            <div class="grow basis-0 border-e px-9 transition-colors max-md:mb-3 max-md:w-full max-md:border-b max-md:border-e-0 max-md:px-0 max-md:pb-3">
                <p class="mb-4 font-medium text-foreground/70">
                    {{ __('Images Left') }}
                </p>
                <p class="text-3xl font-semibold leading-none text-heading-foreground">
                    @if (Auth::user()->remaining_images == -1)
                    @lang('Unlimited')
                    @else
                    {{ number_format((int) Auth::user()->remaining_images) }}
                    @endif
                </p>
            </div>
            @endif
            <div class="grow basis-0 px-9 transition-colors max-md:w-full max-md:p-0">
                <p class="mb-4 font-semibold text-foreground/70">
                    {{ __('Hours Saved') }}
                </p>
                <p class="text-3xl font-semibold leading-none text-heading-foreground">
                    {{ number_format(($total_words * 0.5) / 60) }}
                </p>
            </div>
        </div>

        <div class="w-full">
            <p class="mb-6 font-medium">
                {{ __('Your Documents') }}
            </p>
            <x-total-docs class="[&_.lqd-progress]:h-4" />
        </div> -->
    </x-card>
</div>
<!-- <div class="py-10">
        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Ticked ID') }}
                    </th>
                    <th>
                        {{ __('Status') }}
                    </th>
                    <th>
                        {{ __('Category') }}
                    </th>
                    <th>
                        {{ __('Subject') }}
                    </th>
                    <th>
                        {{ __('Priority') }}
                    </th>
                    <th>
                        {{ __('Created At') }}
                    </th>
                    <th>
                        {{ __('Last Updated') }}
                    </th>
                    <th class="text-end">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($items as $entry)
                    <tr>
                        <td>
                            {{ $entry->ticket_id }}
                        </td>
                        <td>
                            <x-badge
                                class="text-2xs"
                                variant="{{ $entry->status === 'Answered' ? 'success' : 'secondary' }}"
                            >
                                {{ __($entry->status) }}
                            </x-badge>
                        </td>
                        <td>
                            {{ __($entry->category) }}
                        </td>
                        <td>
                            {{ __($entry->subject) }}
                        </td>
                        <td>
                            {{ __($entry->priority) }}
                        </td>
                        <td>
                            {{ $entry->created_at }}
                        </td>
                        <td>
                            {{ $entry->updated_at }}
                        </td>
                        <td class="whitespace-nowrap text-end">
                            <x-button
                                size="sm"
                                href="{{ route('dashboard.support.view', $entry->ticket_id) }}"
                            >
                                {{ __('View') }}
                            </x-button>
                        </td>
                    </tr>
                @endforeach

            </x-slot:body>
        </x-table>
    </div> -->
@endsection

@push('script')
<script src="{{ custom_theme_url('/assets/libs/tom-select/dist/js/tom-select.base.min.js') }}"></script>
<script src="{{ custom_theme_url('/assets/js/panel/support.js') }}"></script>
@endpush