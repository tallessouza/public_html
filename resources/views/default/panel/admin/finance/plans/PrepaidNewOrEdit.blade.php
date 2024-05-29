@extends('panel.layout.settings', ['layout' => 'fullwidth', 'disable_tblr' => true])
@section('title', (isset($subscription) ? __('Edit') : __('Create')) . ' ' . __('Token Pack'))
@section('titlebar_actions', '')

@section('settings')
    <div class="w-full space-y-9">
        <form
            class="flex w-full flex-wrap items-start justify-between gap-y-5"
            id="item_edit_form"
            onsubmit="return prepaidSave({{ $subscription->id ?? null }});"
        >
            <div class="flex w-full flex-wrap justify-between gap-y-5 lg:w-[48%]">
                @if ($isActiveGateway == 0)
                    <x-alert class="mb-3">
                        <p>
                            {{ __('Please enable at least one gateway!') }}
                        </p>
                    </x-alert>
                @endif

                @if (\App\Models\Gateways::query()->where('code', 'coingate')->count() || \App\Models\Gateways::query()->where('code', 'yokassa')->count() || \App\Models\Gateways::query()->where('code', 'razorpay')->count())
                    <x-alert class="mb-2 mt-3">
                        <p>
                            {{ __('Congate, Razorpay or Yookassa subscriptions require you to set up cron jobs on your server. You can find detailed instructions in ') }}
                            <a
                                class="underline"
                                href="https://magicaidocs.liquid-themes.com/how-to-configure-cron-jobs-on-cpanel/"
                            >
                                {{ __('the documentation.') }}
                            </a>

                            @if (\App\Models\Gateways::query()->where('code', 'razorpay')->count())
                                <p>
                                    {{ __('If you use razorpay, don\'t forget to add a webhook. ' . \App\Helpers\Classes\Helper::setting('site_url') . '/webhooks/razorpay') }}
                                </p>
                            @endif
                        </p>
                    </x-alert>
                @endif

                <x-forms.input
                    class:container="w-full"
                    id="name"
                    name="name"
                    label="{{ __('Plan Name') }}"
                    size="lg"
                    placeholder="{{ __('Plan Name') }}"
                    value="{{ isset($subscription) ? $subscription->name : null }}"
                    required
                />

                <x-forms.input
                    class:container="w-full md:w-[48%]"
                    id="price"
                    type="number"
                    min="0"
                    step="0.01"
                    name="price"
                    value="{{ isset($subscription) ? $subscription->price : null }}"
                    required
                    label="{{ __('Price') }}"
                    size="lg"
                />

                <x-forms.input
                    class:container="w-full md:w-[48%]"
                    id="is_featured"
                    name="is_featured"
                    required
                    type="select"
                    size="lg"
                    label="{{ __('Featured Plan') }}"
                >
                    @if (isset($subscription))
                        <option
                            value="1"
                            @selected($subscription->is_featured == 1)
                        >
                            {{ __('Yes') }}</option>
                        <option
                            value="0"
                            @selected($subscription->is_featured == 0)
                        >
                            {{ __('No') }}</option>
                    @else
                        <option value="0">
                            {{ __('No') }}
                        </option>
                        <option value="1">
                            {{ __('Yes') }}
                        </option>
                    @endif
                </x-forms.input>

                <div class="w-full space-y-2 md:w-[48%]">
                    <x-forms.input
                        class:container="w-full"
                        id="display_word"
                        label="{{ __('Total Words') }}"
                        type="checkbox"
                        name="display_word"
                        :checked="(isset($subscription) && $subscription?->display_word_count) || !isset($subscription)"
                        tooltip="{{ __('ON to display word count in plans') }}"
                        switcher
                    />

                    <x-forms.input
                        id="total_words"
                        type="number"
                        name="total_words"
                        size="lg"
                        value="{{ isset($subscription) ? $subscription->total_words : null }}"
                    />
                </div>

                <div class="w-full space-y-2 md:w-[48%]">
                    <x-forms.input
                        class:container="w-full"
                        id="display_img"
                        type="checkbox"
                        name="display_img"
                        label="{{ __('Total Images') }}"
                        :checked="(isset($subscription) && $subscription?->display_imag_count) || !isset($subscription)"
                        tooltip="{{ __('ON to display image count in plans') }}"
                        switcher
                    />

                    <x-forms.input
                        id="total_images"
                        type="number"
                        name="total_images"
                        value="{{ isset($subscription) ? $subscription->total_images : null }}"
                        size="lg"
                    />
                </div>

                <x-alert class="-mt-2 w-full">
                    <p>
                        {{ __('Enter -1 for unlimited usage.') }}
                    </p>
                </x-alert>

                <x-forms.input
                    class:container="w-full"
                    id="features"
                    type="textarea"
                    name="features"
                    cols="30"
                    rows="10"
                    required
                    size="lg"
                    label="{{ __('Features (Comma Seperated)') }}"
                >{{ isset($subscription) ? $subscription->features : null }}</x-forms.input>

                @if ($isActiveGateway == 0)
                    <div class="flex flex-wrap items-center gap-2 rounded-xl bg-amber-100 p-3 text-amber-600 dark:bg-amber-600/20 dark:text-amber-200">
                        <x-tabler-info-circle class="size-5 shrink-0" />
                        {{ __('Please enable at least one gateway!') }}
                    </div>
                @else
                    <x-button
                        class="w-full"
                        id="item_edit_button"
                        type="submit"
                        size="lg"
                    >
                        {{ __('Save') }}
                    </x-button>
                @endif
            </div>

            <div class="w-full lg:w-[48%]">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <h4>
                        <x-tabler-info-circle-filled class="size-4 inline opacity-30" />
                        {{ __('Choose Available Templates') }}
                    </h4>
                    <x-button
                        class="group font-bold text-primary"
                        id="select_all_button"
                        variant="link"
                        onclick="return selectAll('{{ $openAiList->count() }}')"
                    >
                        <span class="group-[&.has-selected]:hidden">
                            @lang('Select All')
                        </span>
                        <span class="hidden group-[&.has-selected]:block">
                            @lang('Deselect All')
                        </span>
                    </x-button>
                    <input
                        id="pages_total_count"
                        type="hidden"
                        value="{{ $openAiList->count() }}"
                    />
                </div>
                @foreach ($openAiList->groupBy('filters') as $key => $items)
                    <x-forms.input
                        class:container="mb-4"
                        id="{{ $key }}"
                        data-filter="check"
                        type="checkbox"
                        label="{{ ucfirst($key) }}"
                        name="display_word"
                        switcher
                    />

                    <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-3">
                        @foreach ($items as $keyItem => $item)
                            <x-forms.input
                                class:container="h-full bg-input-background"
                                class:label="w-full border h-full rounded px-3 py-4 hover:bg-foreground/5 transition-colors"
                                class="checked-item"
                                id="flex_check_{{ $item->id }}"
                                data-filter="{{ $key }}"
                                :checked="in_array($item->slug, $selectedAiList)"
                                type="checkbox"
                                name="openaiItems[]"
                                value="{{ $item->slug }}"
                                label="{{ $item->title }}"
                                custom
                            />
                        @endforeach
                    </div>
                @endforeach
            </div>
        </form>

        <!-- WHAT HAPPENS WHEN YOU SAVE -->
        @if (isset($subscription))
            <x-alert class="space-y-3">
                <p>
                    {{ __('What happens when you save?') }}
                </p>
                <ul class="mb-3 list-inside list-disc">
                    <li>{{ __('Save your settings.') }}</li>
                    <li>{{ __('Check all subscriptions for this plan.') }}</li>
                    <li>{{ __('Remove all products and prices defined before for old settings.') }}</li>
                    <li>{{ __('Cancel all old subscriptions. Acquired amounts do not reset.') }}</li>
                    <li>{{ __('Generate new product definitions in your gateway accounts.') }}</li>
                    <li>{{ __('Generate new price definitions in your gateway accounts.') }}</li>
                </ul>
                <p>
                    {{ __('This process will take time. So, please be patient and wait until success message appears.') }}
                </p>
            </x-alert>
        @else
            <x-alert class="space-y-3">
                <p>
                    {{ __('What happens when you save?') }}
                </p>
                <ul class="mb-3 list-inside list-disc">
                    <li>{{ __('Save your settings.') }}</li>
                    <li>{{ __('Generate new product definitions in your gateway accounts.') }}</li>
                    <li>{{ __('Generate new price definitions in your gateway accounts.') }}</li>
                </ul>
                <p>
                    {{ __('This process will take time. So, please be patient and wait until success message appears.') }}
                </p>
            </x-alert>
        @endif

        @if (isset($generatedData))
            <div class="mt-5">
                <h4 class="mb-4">
                    {{ __('These values are generated for you') }}
                </h4>

                <x-table class="text-2xs">
                    <x-slot:head>
                        <th>
                            {{ __('Gateway') }}
                        </th>
                        <th>
                            {{ __('Product ID') }}
                        </th>
                        <th>
                            {{ __('Plan / Price ID') }}
                        </th>
                    </x-slot:head>
                    <x-slot:body>
                        @foreach ($generatedData as $data)
                            <tr class="even:bg-foreground/5">
                                <td>
                                    {{ $data->gateway_title }}
                                </td>
                                <td>
                                    {{ $data->product_id }}
                                </td>
                                <td>
                                    {{ $data->price_id }}
                                </td>
                            </tr>
                        @endforeach
                    </x-slot:body>
                </x-table>
            </div>
        @endif

    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/finance.js') }}"></script>
    <script>
        function selectAll(total_count) {
            let count = $('.checked-item:checked').length;

            let pages_total_count = $('#pages_total_count').val();

            if (count == pages_total_count) {
                $('.checked-item').prop('checked', false);
                $('#select_all_button').removeClass('has-selected');
            } else {
                $('#select_all_button').addClass('has-selected');
                $('.checked-item').prop('checked', true);
            }

            return false;
        }

        $('[data-filter="check"]').on('change', function() {

            if ($(this).is(':checked')) {
                $('[data-filter="' + $(this).attr('id') + '"]').prop('checked', true);
            } else {
                $('[data-filter="' + $(this).attr('id') + '"]').prop('checked', false);
            }
        });

        let count = $('.checked-item:checked').length;

        let pages_total_count = $('#pages_total_count').val();

        if (pages_total_count == count) {
            $('#select_all_button').addClass('has-selected');
        }

        $('.checked-item').on('change', function() {
            let count = $('.checked-item:checked').length;

            let pages_total_count = $('#pages_total_count').val();

            if (count == pages_total_count) {
                $('#select_all_button').addClass('has-selected');
            } else {
                $('#select_all_button').removeClass('has-selected');
            }
        });
    </script>
@endpush
