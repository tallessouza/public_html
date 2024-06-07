@php
    $plan = Auth::user()->activePlan();
    $plan_type = 'regular';

    if ($plan != null) {
        $plan_type = strtolower($plan->plan_type);
    }
@endphp

@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Dashboard'))
@section('titlebar_title')
    {{ __('Welcome') }}, {{ auth()->user()->name }} 👋🏻
@endsection

@section('content')
    <div class="flex flex-wrap justify-between gap-y-8 pt-10">
        <div class="w-full xl:w-1/3 2xl:w-[30%]">
            @include('panel.user.finance.subscriptionStatus')
        </div>

        @if ($ongoingPayments != null)
            <div class="w-full">
                @include('panel.user.finance.ongoingPayments')
            </div>
        @endif

        <div class="w-full xl:w-[65%] 2xl:w-[67%]">
            <x-card
                class="flex min-h-full flex-col text-xs"
                class:head="border-b-0 pt-8 pb-0 px-8"
                class:body="flex flex-wrap px-8 pt-5 pb-8 grow"
                size="lg"
            >
                <x-slot:head
                    class="flex items-center justify-between gap-2"
                >
                    <h2 class="m-0 flex items-center gap-4">
                        {{-- blade-formatter-disable --}}
						<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect width="40" height="40" rx="8" fill="#6244BB" fill-opacity="0.12"/>
							<path d="M14.83 21.3522C13.1525 21.3522 11.7775 22.718 11.7775 24.4047C11.7775 26.0913 13.1434 27.4572 14.83 27.4572C16.5075 27.4572 17.8825 26.0913 17.8825 24.4047C17.8825 22.718 16.5075 21.3522 14.83 21.3522Z" fill="#6244BB"/>
							<path d="M24.2351 24.643C22.8143 24.643 21.6593 25.798 21.6593 27.2188C21.6593 28.6397 22.8143 29.7947 24.2351 29.7947C25.6559 29.7947 26.8109 28.6397 26.8109 27.2188C26.8109 25.798 25.6559 24.643 24.2351 24.643Z" fill="#6244BB"/>
							<path d="M23.2908 10.6042C20.5683 10.6042 18.3592 12.8134 18.3592 15.5359C18.3592 18.2584 20.5683 20.4675 23.2908 20.4675C26.0133 20.4675 28.2225 18.2584 28.2225 15.5359C28.2225 12.8134 26.0133 10.6042 23.2908 10.6042Z" fill="#6244BB"/>
						</svg>
						{{-- blade-formatter-enable --}}
                        {{ __('Overview') }}
                    </h2>
                    <x-badge class="px-5 py-2.5 text-2xs text-foreground max-md:hidden">
                        @lang('Valores dos Documentos')
                    </x-badge>
                </x-slot:head>

                <div class="mb-5 lg:w-1/2">
                    <p>
                        @lang('Entenda e gerencie seus projetos da melhor forma. Mergulhe em detalhes relevantes.')
                    </p>
                </div>

                <div class="mb-6 flex w-full border-b pb-6 max-md:flex-col">
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
                            {{ number_format(($total_words * 0.5) / 60) }}</p>
                    </div>
                </div>

                <div class="w-full">
                    <p class="mb-6 font-medium">
                        {{ __('Your Documents') }}
                    </p>
                    <x-total-docs class="[&_.lqd-progress]:h-4" />
                </div>
            </x-card>
        </div>

        <div class="w-full">
            <x-card
                class="flex min-h-full flex-col text-xs"
                class:head="pt-8 px-0 mx-8 border-border"
                class:body="flex flex-wrap px-8 pt-2 pb-8 grow"
                size="lg"
            >
                <x-slot:head
                    class="flex items-center justify-between gap-2"
                >
                    <h2 class="m-0 flex items-center gap-4">
                        {{-- blade-formatter-disable --}}
						<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect width="40" height="40" rx="8" fill="#6244BB" fill-opacity="0.12"/>
							<path d="M21.1917 16.4343L22.4017 18.8543C22.5667 19.1843 23.0067 19.5143 23.3733 19.5693L25.5642 19.9359C26.9667 20.1743 27.2967 21.1826 26.2883 22.1909L24.5833 23.8959C24.2992 24.1801 24.1342 24.7393 24.2258 25.1426L24.7117 27.2601C25.0967 28.9284 24.2075 29.5793 22.7317 28.7084L20.6783 27.4893C20.3025 27.2693 19.6975 27.2693 19.3217 27.4893L17.2683 28.7084C15.7925 29.5793 14.9033 28.9284 15.2883 27.2601L15.7742 25.1426C15.8658 24.7484 15.7008 24.1893 15.4166 23.8959L13.7117 22.1909C12.7033 21.1826 13.0333 20.1651 14.4358 19.9359L16.6266 19.5693C16.9933 19.5051 17.4333 19.1843 17.5983 18.8543L18.8083 16.4343C19.4592 15.1234 20.5408 15.1234 21.1917 16.4343Z" fill="#6244BB"/>
							<path d="M14.5 17.9375C14.1242 17.9375 13.8125 17.6258 13.8125 17.25V10.8333C13.8125 10.4575 14.1242 10.1458 14.5 10.1458C14.8758 10.1458 15.1875 10.4575 15.1875 10.8333V17.25C15.1875 17.6258 14.8758 17.9375 14.5 17.9375Z" fill="#6244BB"/>
							<path d="M25.5 17.9375C25.1242 17.9375 24.8125 17.6258 24.8125 17.25V10.8333C24.8125 10.4575 25.1242 10.1458 25.5 10.1458C25.8758 10.1458 26.1875 10.4575 26.1875 10.8333V17.25C26.1875 17.6258 25.8758 17.9375 25.5 17.9375Z" fill="#6244BB"/>
							<path d="M20 13.3542C19.6242 13.3542 19.3125 13.0425 19.3125 12.6667V10.8333C19.3125 10.4575 19.6242 10.1458 20 10.1458C20.3758 10.1458 20.6875 10.4575 20.6875 10.8333V12.6667C20.6875 13.0425 20.3758 13.3542 20 13.3542Z" fill="#6244BB"/>
						</svg>
						{{-- blade-formatter-enable --}}
                        {{ __('Favorite Documents') }}
                    </h2>

                    <a
                        class="text-2xs underline max-md:hidden"
                        href="{{ route('dashboard.user.openai.documents.all') }}"
                    >
                        @lang('Ver todos os Documentos')
                    </a>
                </x-slot:head>

                <x-table
                    class="text-xs"
                    variant="none"
                >
                    <x-slot:head
                        class="text-xs font-normal normal-case tracking-normal text-foreground [&_th]:font-normal [&_th]:first:ps-0 [&_th]:last:pe-0"
                    >
                        <th>
                            @lang('Informações do Documento')
                        </th>
                        <th>
                            @lang('Categoria')
                        </th>
                        <th>
                            @lang('Em')
                        </th>
                        <th>
                            @lang('Data')
                        </th>
                    </x-slot:head>
                    <x-slot:body
                        class="[&_td]:px-0"
                    >
                        @foreach (\Illuminate\Support\Facades\Auth::user()->favoriteOpenai as $entry)
                            @php
                                $upgrade = false;
                                if ($entry->premium == 1 && $plan_type === 'regular') {
                                    $upgrade = true;
                                }

                                if ($upgrade) {
                                    $href = LaravelLocalization::localizeUrl(route('dashboard.user.payment.subscription'));
                                } else {
                                    $href = LaravelLocalization::localizeUrl(
                                        route($entry->type === 'voiceover' ? 'dashboard.user.openai.generator.workbook' : 'dashboard.user.openai.generator', $entry->slug),
                                    );
                                }
                            @endphp
                            <tr @class([
                                'relative',
                                'transition-colors hover:bg-foreground/5' =>
                                    $upgrade || $entry->active == 1,
                            ])>
                                <td>
                                    <a
                                        class="flex items-center gap-2"
                                        href="{{ $href }}"
                                    >
                                        <x-lqd-icon
                                            size="sm"
                                            style="background: {{ $entry->color }}"
                                            active-badge
                                            active-badge-condition="{{ $entry->active == 1 }}"
                                        >
                                            <span class="size-3.5 flex">
                                                @if ($entry->image !== 'none')
                                                    {!! html_entity_decode($entry->image) !!}
                                                @endif
                                            </span>
                                        </x-lqd-icon>
                                        <span class="block text-xs text-heading-foreground">
                                            {{ __($entry->title) }}
                                        </span>
                                    </a>
                                </td>
                                <td>
                                    {{ @ucfirst($entry->type) }}
                                </td>
                                <td class="text-heading-foreground">
                                    @lang('In Workbook')
                                </td>
                                <td>
                                    <span class="opacity-80">
                                        {{ $entry->created_at->format('M d, Y') }}
                                    </span>
                                </td>
                                <td
                                    class="w-0 p-0"
                                    colspan="0"
                                >
                                    @if ($upgrade)
                                        <span class="absolute inset-0 flex items-center justify-center bg-background/50">
                                            <x-badge
                                                class="rounded-md py-1.5"
                                                variant="info"
                                            >
                                                {{ __('Upgrade') }}
                                            </x-badge>
                                        </span>
                                    @endif
                                    @if ($upgrade || $entry->active == 1)
                                        <a
                                            class="absolute inset-0 max-sm:hidden"
                                            href="{{ $href }}"
                                        >
                                            <span class="sr-only">
                                                @if ($upgrade)
                                                    {{ __('Upgrade') }}
                                                @elseif ($entry->active == 1)
                                                    {{ __('View') }}
                                                @endif
                                            </span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @if ($loop->iteration == 4)
                            @break
                        @endif
                    @endforeach
                </x-slot:body>
            </x-table>
        </x-card>
    </div>

    <div class="w-full">
        <x-card
            class="flex min-h-full flex-col text-xs"
            class:head="pt-8 px-0 mx-8 border-border"
            class:body="flex flex-wrap px-8 pt-2 pb-8 grow"
            size="lg"
        >
            <x-slot:head
                class="flex items-center justify-between gap-2"
            >
                <h2 class="m-0 flex items-center gap-4">
                    {{-- blade-formatter-disable --}}
						<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect width="40" height="40" rx="8" fill="#6244BB" fill-opacity="0.12"/>
							<path d="M25.8213 27.0825C25.3171 28.375 24.0888 29.2092 22.7046 29.2092H17.2963C15.9029 29.2092 14.6838 28.375 14.1796 27.0825C13.6754 25.7809 14.0238 24.3417 15.0504 23.4067L18.763 20.0425H21.2471L24.9505 23.4067C25.9771 24.3417 26.3163 25.7809 25.8213 27.0825Z" fill="#6244BB"/>
							<path d="M21.6683 25.6283H18.3317C17.9833 25.6283 17.7083 25.3442 17.7083 25.005C17.7083 24.6567 17.9925 24.3817 18.3317 24.3817H21.6683C22.0167 24.3817 22.2917 24.6658 22.2917 25.005C22.2917 25.3442 22.0075 25.6283 21.6683 25.6283Z" fill="#6244BB"/>
							<path d="M25.8206 12.96C25.3164 11.6675 24.0881 10.8333 22.7039 10.8333H17.2955C15.9114 10.8333 14.683 11.6675 14.1789 12.96C13.6839 14.2617 14.023 15.7008 15.0589 16.6358L18.7622 20H21.2464L24.9497 16.6358C25.9764 15.7008 26.3156 14.2617 25.8206 12.96ZM21.6681 15.6275H18.3314C17.983 15.6275 17.708 15.3433 17.708 15.0042C17.708 14.665 17.9922 14.3808 18.3314 14.3808H21.6681C22.0164 14.3808 22.2914 14.665 22.2914 15.0042C22.2914 15.3433 22.0072 15.6275 21.6681 15.6275Z" fill="#6244BB"/>
						</svg>
						{{-- blade-formatter-enable --}}
                    {{ __('Recently Launched Documents') }}
                </h2>

                <a
                    class="text-2xs underline max-md:hidden"
                    href="{{ route('dashboard.user.openai.documents.all') }}"
                >
                    @lang('Ver todos os Documentos')
                </a>
            </x-slot:head>

            <x-table
                class="text-xs"
                variant="none"
            >
                <x-slot:head
                    class="text-xs font-normal normal-case tracking-normal text-foreground [&_th]:font-normal [&_th]:first:ps-0 [&_th]:last:pe-0"
                >
                    <th>
                        @lang('Informações de Templates')
                    </th>
                    <th>
                        @lang('Categoria')
                    </th>
                    <th>
                        @lang('Em')
                    </th>
                    <th>
                        @lang('Data')
                    </th>
                </x-slot:head>
                <x-slot:body
                    class="[&_td]:px-0"
                >
                    @foreach (Auth::user()->openai()->orderBy('created_at', 'desc')->take(4)->get() as $entry)
                        @if ($entry->generator != null)
                            <tr class="relative transition-colors hover:bg-foreground/5">
                                <td>
                                    <a
                                        class="flex items-center gap-2"
                                        href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.single', $entry->slug)) }}"
                                    >
                                        <x-lqd-icon
                                            size="sm"
                                            style="background: {{ $entry->generator->color }}"
                                        >
                                            <span class="size-3.5 flex">
                                                @if ($entry->generator->image !== 'none')
                                                    {!! html_entity_decode($entry->generator->image) !!}
                                                @endif
                                            </span>
                                        </x-lqd-icon>
                                        <span class="block text-xs text-heading-foreground">
                                            {{ __($entry->generator->title) }}
                                        </span>
                                    </a>
                                </td>
                                <td>
                                    {{ @ucfirst($entry->generator->type) }}
                                </td>
                                <td class="text-heading-foreground">
                                    @lang('In Workbook')
                                </td>
                                <td>
                                    <span class="opacity-80">
                                        {{ $entry->created_at->format('M d, Y') }}
                                    </span>
                                </td>
                                <td
                                    class="w-0 p-0"
                                    colspan="0"
                                >
                                    <a
                                        class="absolute inset-0 max-sm:hidden"
                                        href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.single', $entry->slug)) }}"
                                    >
                                        <span class="sr-only">
                                            {{ __('View') }}
                                        </span>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </x-slot:body>
            </x-table>
        </x-card>
    </div>
</div>
@endsection
