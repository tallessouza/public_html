<div @class([
    'w-full rounded-3xl border bg-background',
    'shadow-[0_7px_20px_rgba(0,0,0,0.04)]' => $plan->is_featured,
])>
    <div class="flex h-full flex-col p-7">

        <h2 class="mb-5 flex items-start leading-none text-heading-foreground">
            @lang('Order Summary')
        </h2>
        <p class="mb-0 mt-1 flex items-start leading-none text-heading-foreground">
            {{ __($plan->name) }} / {{ $plan->type == 'prepaid' ? __('One time') : __(formatCamelCase($plan->frequency)) }} @lang('Plan')
        <div class="ms-2 inline-flex flex-col items-start gap-2 text-[0.3em]">
            @if ($plan->is_featured == 1)
                <div class="inline-flex rounded-full bg-gradient-to-r from-[#ece7f7] via-[#e7c5e6] to-[#e7ebf9] px-3 py-1 text-3xs text-black">
                    {{ __('Popular plan') }}
                </div>
            @endif
        </div>
        </p>

        <ul class="list-unstyled">
            <hr>
            <li class="mb-[0.625em] flex">
                <div class="flex-1 text-start">{{ __('Subtotal') }}</div>
                <div class="flex-1 text-end">{!! displayCurr(currency()->symbol, $plan->price, 0, $newDiscountedPrice ?? null) !!}</div>
            </li>
            <li class="mb-[0.625em] flex">
                <div class="flex-1 text-start">{{ __('Tax') }} ({{ $taxRate ?? 0 }}%)</div>
                <div class="flex-1 text-end">{!! displayCurr(currency()->symbol, $taxValue ?? null) !!}</div>
            </li>
            <li class="mb-[0.625em] flex">
                <div class="flex-1 text-start">{{ __('Total') }}</div>
                <div class="flex-1 text-end">{!! displayCurr(currency()->symbol, $plan->price, $taxValue ?? 0, $newDiscountedPrice ?? null) !!}</div>
            </li>
            <hr>
        </ul>

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
                            @foreach (\App\Models\OpenAIGenerator::query()->get()->groupBy('filters') as $key => $openAi)
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

        <div class="mt-auto text-center">
            <a
                class="btn w-full rounded-md p-[1.15em_2.1em] text-[15px] group-[.theme-dark]/body:!bg-[rgba(255,255,255,1)] group-[.theme-dark]/body:!text-[rgba(0,0,0,0.9)]"
                href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.payment.subscription')) }}"
            >{{ __('Change Plan') }}</a>
        </div>
    </div>
</div>
