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
    {{ __('Welcome') }}, {{ auth()->user()->name }}.
@endsection

@section('content')
    <div class="flex flex-wrap justify-between gap-8 pt-10">
        <div class="w-full">
            @include('panel.user.finance.subscriptionStatus')
        </div>

        @if ($ongoingPayments != null)
            <div class="w-full">
                @include('panel.user.finance.ongoingPayments')
            </div>
        @endif

        <x-card
            class:body="flex flex-wrap gap-y-6"
            size="lg"
        >
            <h3 class="mb-5 w-full">{{ __('Overview') }}</h3>
            <div class="justify-betweenmax-lg:w-full flex grow basis-0 items-center max-lg:basis-full max-lg:flex-wrap sm:flex-row">
                <div class="grow basis-0 border-e px-9 !ps-0 transition-colors max-lg:mb-3 max-lg:basis-full max-lg:border-b max-lg:border-e-0 max-lg:px-0 max-lg:pb-3">
                    <p class="mb-4 font-medium text-foreground/70">{{ __('Words Left') }}</p>
                    <p class="text-3xl font-semibold leading-none text-heading-foreground">
                        @if (Auth::user()->remaining_words == -1)
                            @lang('Unlimited')
                        @else
                            {{ number_format((int) Auth::user()->remaining_words) }}
                        @endif
                    </p>
                </div>
                @if ($setting->feature_ai_image)
                    <div class="grow basis-0 border-e px-9 transition-colors max-lg:mb-3 max-lg:basis-full max-lg:border-b max-lg:border-e-0 max-lg:px-0 max-lg:pb-3">
                        <p class="mb-4 font-medium text-foreground/70">{{ __('Images Left') }}</p>
                        <p class="text-3xl font-semibold leading-none text-heading-foreground">
                            @if (Auth::user()->remaining_images == -1)
                                @lang('Unlimited')
                            @else
                                {{ number_format((int) Auth::user()->remaining_images) }}
                            @endif
                        </p>
                    </div>
                @endif
                <div class="grow basis-0 px-9 transition-colors max-lg:basis-full max-lg:px-0 max-sm:mb-3 max-sm:pb-3">
                    <p class="mb-0 font-semibold text-foreground/70">{{ __('Hours Saved') }}</p>
                    <p class="text-3xl font-semibold leading-none text-heading-foreground">
                        {{ number_format(($total_words * 0.5) / 60) }}</p>
                </div>
            </div>
            <div class="grow basis-0 max-lg:w-full max-lg:basis-full lg:ps-5">
                <p class="mb-2 font-medium text-foreground/70">{{ __('Your Documents') }}</p>

                <x-total-docs />
            </div>
        </x-card>

        <div class="grow basis-full md:basis-0">
            <x-card size="none">
                <x-slot:head>
                    <h4 class="m-0">{{ __('Documents') }}</h4>
                </x-slot:head>
                @foreach (Auth::user()->openai()->with('generator')->orderBy('created_at', 'desc')->take(4)->get() as $entry)
                    @if ($entry->generator != null)
                        <x-documents.item :$entry />
                    @endif
                @endforeach
            </x-card>
        </div>

        <div class="grow basis-full md:basis-0">
            <x-card size="none">
                <x-slot:head>
                    <h4 class="m-0">{{ __('Favorite Templates') }}</h4>
                </x-slot:head>
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
                    @if ($upgrade || $entry->active == 1)
                        <a
                            class="lqd-fav-temp-item relative flex w-full flex-wrap items-center gap-3 border-b p-4 text-xs transition-colors last:border-none hover:bg-foreground/5"
                            href="{{ $href }}"
                        >
                        @else
                            <p class="lqd-fav-temp-item relative flex w-full flex-wrap items-center gap-3 border-b p-4 text-xs last:border-none">
                    @endif
                    <x-lqd-icon
                        size="lg"
                        style="background: {{ $entry->color }}"
                        active-badge
                        active-badge-condition="{{ $entry->active == 1 }}"
                    >
                        <span class="size-5 flex">
                            @if ($entry->image !== 'none')
                                {!! html_entity_decode($entry->image) !!}
                            @endif
                        </span>
                    </x-lqd-icon>
                    <span class="w-2/5 grow">
                        <span class="lqd-fav-temp-item-title block text-sm font-medium">
                            {{ __($entry->title) }}
                        </span>
                        <span class="lqd-fav-temp-item-desc opacity-45 block max-w-full overflow-hidden overflow-ellipsis whitespace-nowrap italic">
                            {{ str()->words(__($entry->description), 5) }}
                        </span>
                    </span>
                    <span class="flex flex-col whitespace-nowrap">
                        {{ __('in Workbook') }}
                        <span class="lqd-fav-temp-item-date opacity-45 italic">
                            {{ $entry->created_at->format('M d, Y') }}
                        </span>
                    </span>
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
                        </a>
                    @else
                        </p>
                    @endif
                    @if ($loop->iteration == 4)
                    @break
                @endif
            @endforeach
        </x-card>
    </div>
</div>
@endsection
