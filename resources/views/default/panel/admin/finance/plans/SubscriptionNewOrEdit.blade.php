@extends('panel.layout.settings', ['layout' => 'fullwidth'])
@section('title', (isset($subscription) ? __('Edit') : __('Create')) . ' ' . __('Subscription'))
@section('titlebar_actions', '')

@section('settings')
    <div class="w-full space-y-9">
        <form
            class="flex w-full flex-wrap items-start justify-between gap-y-5"
            id="item_edit_form"
            onsubmit="return subscriptionSave({{ $subscription->id ?? null }});"
        >
            <div class="flex w-full flex-wrap justify-between gap-y-5 lg:w-[48%]">
                @if ($isActiveGateway == 0)
                    <x-alert class="mb-3">
                        <p>
                            {{ __('Please enable at least one gateway!') }}
                        </p>
                    </x-alert>
                @endif

                @if (
                    \App\Models\Gateways::query()->where('code', 'coingate')->count() ||
                        \App\Models\Gateways::query()->where('code', 'yokassa')->count() ||
                        \App\Models\Gateways::query()->where('code', 'razorpay')->count())
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

                <div
                    class="w-full"
                    id="cron-alert"
                    role="alert"
                    style="display: none"
                >
                    <x-alert class="mb-3 mt-2">
                        <p>
                            {!! __(
                                '<b>Free</b> and <b>Lifetime</b> plans require you to set up cron jobs on your server and <b>Trial Days</b> cannot be set for these plans. You can find detailed instructions in the',
                            ) !!} <a
                                class="underline"
                                target="_blank"
                                href="https://magicaidocs.liquid-themes.com/how-to-configure-cron-jobs-on-cpanel"
                            >
                                {{ __('documentation') }}
                            </a>.
                        </p>
                    </x-alert>
                </div>

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
                    id="trial_days"
                    name="trial_days"
                    value="{{ isset($subscription) ? $subscription->trial_days : 0 }}"
                    required
                    type="select"
                    size="lg"
                    label="{{ __('Trial Days') }}"
                    tooltip="{{ __('Trial days cannot be set for free and lifetime plans.') }}"
                >
                    @php($selectedValue = isset($subscription) ? (isset($subscription->trial_days) ? $subscription->trial_days : '0') : '0')
                    <option value="7">
                        {{ __('7 Days') }}
                    </option>
                    <option value="14">
                        {{ __('14 Days') }}
                    </option>
                    <option
                        value="0"
                        @selected($selectedValue == 0)
                    >
                        {{ __('0 - No Trial') }}
                    </option>
                    <option
                        value="1"
                        @selected($selectedValue == 1)
                    >
                        {{ __('1 Day') }}
                    </option>
                    @for ($i = 2; $i <= 28; $i++)
                        <option
                            value="{{ $i }}"
                            @selected($selectedValue == $i)
                        >
                            {{ __("$i Days") }}
                        </option>
                    @endfor
                </x-forms.input>

                <x-forms.input
                    class:container="w-full md:w-[48%]"
                    id="frequency"
                    name="frequency"
                    required
                    type="select"
                    size="lg"
                    label="{{ __('Renewal Type') }}"
                >
                    @if (isset($subscription))
                        <option
                            value="monthly"
                            @selected($subscription->frequency == 'monthly')
                        >
                            {{ __('Monthly') }}
                        </option>
                        <option
                            value="yearly"
                            @selected($subscription->frequency == 'yearly')
                        >
                            {{ __('Yearly') }}
                        </option>
                        <option
                            value="lifetime_monthly"
                            @selected($subscription->frequency == 'lifetime_monthly')
                        >
                            {{ __('Lifetime - Monthly Renewal') }}
                        </option>
                        <option
                            value="lifetime_yearly"
                            @selected($subscription->frequency == 'lifetime_yearly')
                        >
                            {{ __('Lifetime - Yearly Renewal') }}
                        </option>
                    @else
                        <option value="monthly">
                            {{ __('Monthly') }}
                        </option>
                        <option value="yearly">
                            {{ __('Yearly') }}
                        </option>
                        <option value="lifetime_monthly">
                            {{ __('Lifetime - Monthly Renewal') }}
                        </option>
                        <option value="lifetime_yearly">
                            {{ __('Lifetime - Yearly Renewal') }}
                        </option>
                    @endif
                </x-forms.input>

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

                <x-forms.input
                    class:container="w-full"
                    id="is_team_plan"
                    type="checkbox"
                    name="is_team_plan"
                    label="{{ __('Is team plan') }}"
                    tooltip="{{ __('A team will be create in this plan') }}"
                    switcher
                />

                <x-forms.input
                    class:container="w-full"
                    id="plan_allow_seat"
                    type="number"
                    name="plan_allow_seat"
                    placeholder="{{ __('Number of Seats') }}"
                    value="{{ isset($subscription) ? $subscription->plan_allow_seat : null }}"
                    size="lg"
                />

                <div class="w-full space-y-2 md:w-5/12">
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

                <div class="w-full space-y-2 md:w-5/12">
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
                    class:container="w-full md:w-[48%]"
                    id="ai_name"
                    label="{{ __('AI Name') }}"
                    name="ai_name"
                    type="select"
                    size="lg"
                    required
                >
                    @if (isset($subscription))
                        <option
                            value="text-davinci-003"
                            @selected($subscription->ai_name == 'text-davinci-003')
                        >
                            {{ __('Davinci') }}
                        </option>
                        <option
                            value="gpt-3.5-turbo"
                            @selected($subscription->ai_name == 'gpt-3.5-turbo')
                        >
                            {{ __('ChatGPT 3.5') }}
                        </option>
                        <option
                            value="gpt-3.5-turbo-16k"
                            @selected($subscription->ai_name == 'gpt-3.5-turbo-16k')
                        >
                            {{ __('ChatGTP (3.5-turbo-16k)') }}
                        </option>
                        <option
                            value="gpt-4"
                            @selected($subscription->ai_name == 'gpt-4')
                        >
                            {{ __('ChatGPT 4') }}
                        </option>
                        <option
                            value="gpt-4-1106-preview"
                            @selected($subscription->ai_name == 'gpt-4-1106-preview')
                        >
                            {{ __('GPT-4 Turbo (Updated Knowleddge cutoff of April 2023, 128k)') }}
                        </option>
                        <option
                            value="gpt-4-0125-preview"
                            @selected($subscription->ai_name == 'gpt-4-0125-preview')
                        >
                            {{ __('GPT-4 Turbo (Updated Knowleddge cutoff of Dec 2023, 128k)') }}
                        </option>
                        <option
                            value="gpt-4-turbo"
                            @selected($subscription->ai_name == 'gpt-4-turbo')
                        >
                            {{ __('GPT-4 Turbo with Vision (Updated Knowleddge cutoff of Dec 2023, 128k)') }}
                        </option>
                        <option
                            value="gpt-4o"
                            @selected($subscription->ai_name == 'gpt-4o')
                        >
                            {{ __('GPT-4o Most advanced, multimodal flagship model that’s cheaper and faster than GPT-4 Turbo.  (Updated Knowleddge cutoff of Oct 2023, 128k)') }}
                        </option>
                    @else
                        <option value="text-davinci-003">
                            {{ __('Davinci') }}
                        </option>
                        <option value="gpt-3.5-turbo">
                            {{ __('ChatGPT 3.5') }}
                        </option>
                        <option value="gpt-3.5-turbo-16k">
                            {{ __('ChatGTP (3.5-turbo-16k)') }}
                        </option>
                        <option
                            value="gpt-4"
                            selected
                        >
                            {{ __('ChatGPT 4') }}
                        </option>
                        <option value="gpt-4-1106-preview">
                            {{ __('GPT-4 Turbo (Updated Knowleddge cutoff of April 2023, 128k)') }}
                        </option>
                        <option value="gpt-4-0125-preview">
                            {{ __('GPT-4 Turbo (Updated Knowleddge cutoff of Dec 2023, 128k)') }}
                        </option>
                        <option value="gpt-4-turbo">
                            {{ __('GPT-4 Turbo with Vision (Updated Knowleddge cutoff of Dec 2023, 128k)') }}
                        </option>
                        <option value="gpt-4o">
                            {{ __('GPT-4o Most advanced, multimodal flagship model that’s cheaper and faster than GPT-4 Turbo.  (Updated Knowleddge cutoff of Oct 2023, 128k)') }}
                        </option>
                    @endif
                </x-forms.input>

                <x-forms.input
                    class:container="w-full md:w-[48%] hidden"
                    id="max_tokens"
                    label="{{ __('Max Tokens') }}"
                    name="max_tokens"
                    type="number"
                    value="{{ isset($subscription) ? $subscription->max_tokens : null }}"
                    size="lg"
                />

                <x-forms.input
                    class:container="w-full md:w-[48%]"
                    id="can_create_ai_images"
                    name="can_create_ai_images"
                    required
                    size="lg"
                    type="select"
                    label="{{ __('Can Create AI Images') }}"
                >
                    @if (isset($subscription))
                        <option
                            value="1"
                            @selected($subscription->can_create_ai_images == 1)
                        >
                            {{ __('Yes') }}
                        </option>
                        <option
                            value="0"
                            @selected($subscription->can_create_ai_images == 0)
                        >
                            {{ __('No') }}
                        </option>
                    @else
                        <option value="1">
                            {{ __('Yes') }}
                        </option>
                        <option value="0">
                            {{ __('No') }}
                        </option>
                    @endif
                </x-forms.input>

                <x-alert>
                    <p>
                        {{ __('Please note GPT-4 is not working with every api_key. You have to have an api key which can work with GPT-4.') }}
                    </p>
                    <p>
                        {{ __('Also please note that Chat models works with ChatGPT and GPT-4 models. So if you choose below it will automatically use ChatGPT.') }}
                    </p>
                </x-alert>

                <x-forms.input
                    class:container="w-full"
                    id="plan_type"
                    name="plan_type"
                    required
                    type="select"
                    size="lg"
                    label="{{ __('Template Access') }}"
                >
                    @if (isset($subscription))
                        <option
                            value="All"
                            @selected($subscription->plan_type == 'All')
                        >
                            {{ __('All') }}
                        </option>
                        <option
                            value="Premium"
                            @selected($subscription->plan_type == 'Premium')
                        >
                            {{ __('Premium') }}
                        </option>
                        <option
                            value="Regular"
                            @selected($subscription->plan_type == 'Regular')
                        >
                            {{ __('Regular') }}
                        </option>
                    @else
                        <option value="All">
                            {{ __('All') }}
                        </option>
                        <option value="Premium">
                            {{ __('Premium') }}
                        </option>
                        <option value="Regular">
                            {{ __('Regular') }}
                        </option>
                    @endif
                </x-forms.input>

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
                        <x-tabler-info-circle class="size-5" />
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
            <x-alert>
                <p>
                    {{ __('What happens when you save?') }}
                </p>
                <ul class="mb-2 list-inside list-disc">
                    <li>{{ __('Save your settings.') }}</li>
                    <li>{{ __('Check all subscriptions for this plan.') }}</li>
                    <li>{{ __('Remove all products and prices defined before for old settings.') }}</li>
                    <li>{{ __('Cancel all old subscriptions. Acquired amounts do not reset.') }}</li>
                    <li>{{ __('Generate new product definitions in your gateway accounts.') }}</li>
                    <li>{{ __('Generate new price definitions in your gateway accounts.') }}</li>
                </ul>
                <p class="flex gap-3">
                    {{ __('This process will take time. So, please be patient and wait until success message appears.') }}
                </p>
            </x-alert>
        @else
            <x-alert>
                <p>
                    {{ __('What happens when you save?') }}
                </p>
                <ul class="mb-2 list-inside list-disc">
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
        document.addEventListener('DOMContentLoaded', function() {
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

            var priceInput = document.getElementById('price');
            var frequencySelect = document.getElementById('frequency');
            var cronAlert = document.getElementById('cron-alert');

            function updateAlertAndBorder() {
                var priceValue = parseFloat(priceInput.value);
                var selectedFrequency = frequencySelect.value;

                if (priceValue === 0 || (selectedFrequency === 'lifetime_monthly' || selectedFrequency ===
                        'lifetime_yearly')) {
                    cronAlert.style.display = 'block';
                    priceInput.classList.add('border-primary');
                    frequencySelect.classList.add('border-primary');
                } else {
                    cronAlert.style.display = 'none';
                    priceInput.classList.remove('border-primary');
                    frequencySelect.classList.remove('border-primary');
                }
            }

            priceInput.addEventListener('input', updateAlertAndBorder);
            frequencySelect.addEventListener('change', updateAlertAndBorder);

            updateAlertAndBorder();
        });
    </script>
@endpush
