@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', $template != null ? __('Edit Custom Template') : __('Add Custom Template'))
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex flex-col gap-10"
        id="custom_template_form"
        @if ($app_is_demo) onsubmit="return toastr.info('This feature is disabled in Demo version.')"
		@else
			onsubmit="return templateSave({{ $template != null ? $template->id : null }});" @endif
    >
        <div class="flex flex-col gap-6">
            <x-form-step
                step="1"
                label="{{ __('Template') }}"
            />

            <x-forms.input
                id="title"
                size="lg"
                name="title"
                label="{{ __('Template Title') }}"
                placeholder="{{ __('Enter Title Here') }}"
                value="{{ $template != null ? $template->title : null }}"
                tooltip="{{ __('A title for the template that will show in templates list and in search results') }}"
            />

            <x-forms.input
                id="description"
                size="lg"
                name="description"
                label="{{ __('Template Description') }}"
                placeholder="{{ __('Enter Description Here') }}"
                type="textarea"
                rows="3"
                tooltip="{{ __('A short description about what this template do.') }}"
            >{{ $template != null ? $template->description : null }}</x-forms.input>

            <x-forms.input
                id="image"
                size="lg"
                name="image"
                label="{{ __('Template Icon') }}"
                placeholder="{{ __('Paste the svg code you get from the Tabler Icons or any other icon sets') }}"
                value="{!! $template != null && $template->image != null && $template->image != '' ? $template->image : '' !!}"
                tooltip="{{ __('Paste the svg code you get from the Tabler Icons or any other icon sets') }}"
            />

            <x-forms.input
                id="color"
                size="lg"
                name="color"
                label="{{ __('Template Color') }}"
                placeholder="{{ __('Template Color') }}"
                type="color"
                tooltip="{{ __('Pick a color for for the icon container shape. Color is in HEX format.') }}"
                value="{{ $template != null ? $template->color : '#8fd2d0' }}"
            />

            <x-forms.input
                id="filters"
                size="none"
                name="filters"
                type="select"
                multiple
                label="{{ __('Template Category') }}"
                placeholder="{{ __('Enter Category Here') }}"
                value="{{ $template != null ? $template->filters : null }}"
                tooltip="{{ __('Categories of the template. Useful for filtering in the templates list.') }}"
                add-new
            >
                @foreach ($filters as $filter)
                    <option
                        value="{{ $filter->name }}"
                        @selected(isset($template) && isset($template->filters) && in_array($filter->name, explode(',', $template->filters)))
                    >
                        {{ $filter->name }}
                    </option>
                @endforeach
            </x-forms.input>

            <x-forms.input
                id="premium"
                size="lg"
                name="premium"
                type="select"
                label="{{ __('Package Type') }}"
                tooltip="{{ __('Choose package type for which plans accessible.') }}"
            >
                <option
                    value="0"
                    @selected($template != null && $template->premium == 0)
                >
                    {{ __('Regular') }}
                </option>
                <option
                    value="1"
                    @selected($template != null && $template->premium == 1)
                >
                    {{ __('Premium') }}
                </option>
            </x-forms.input>
        </div>

        <div class="flex flex-col gap-5">
            <x-form-step
                step="2"
                label="{{ __('Input Groups') }}"
            >
                <x-button
                    class="add-more size-8 ms-auto dark:bg-white dark:text-black"
                    size="none"
                    variant="ghost-shadow"
                    type="button"
                >
                    <x-tabler-plus class="size-4" />
                </x-button>
            </x-form-step>

            @if ($template != null)
                <?php $question_i = 1; ?>
                @foreach (json_decode($template->questions) ?? [] as $question)
                    <x-card
                        class="user-input-group relative"
                        class:body="flex flex-col gap-5"
                        data-input-name="{{ $question->question }}"
                        data-inputs-id="{{ $question_i }}"
                    >
                        <x-forms.input
                            class="input_type"
                            id="input_type"
                            size="lg"
                            name="input_type"
                            type="select"
                            label="{{ __('Select Input Type') }}"
                            tooltip="{{ __('Input fields for short texts and Textarea fields are good for long text.') }}"
                        >
                            <option
                                value="text"
                                @selected($question->type == 'text')
                            >
                                {{ __('Input Field') }}
                            </option>
                            <option
                                value="textarea"
                                @selected($question->type == 'textarea')
                            >
                                {{ __('Textarea Field') }}
                            </option>
                            <option
                                value="select"
                                @selected($question->type == 'select')
                            >
                                {{ __('Selectlist Field') }}
                            </option>
                        </x-forms.input>

                        <x-forms.input
                            class="input_name"
                            id="input_name"
                            size="lg"
                            name="input_name"
                            label="{{ __('Input Name') }}"
                            placeholder="{{ __('Enter Name Here') }}"
                            value="{{ $question->question ?? '' }}"
                            tooltip="{{ __('Unique input name that you can use in your prompts later.') }}"
                        />

                        <x-forms.input
                            class="input_description"
                            id="input_description"
                            size="lg"
                            name="input_description"
                            label="{{ __('Input Description') }}"
                            placeholder="{{ __('Enter Description Here') }}"
                            value="{{ $question->description ?? '' }}"
                            tooltip="{{ __('A description for the input.') }}"
                        />
                        <div class="selectlistinputs hidden">
                            <x-forms.input
                                class="selectlistinputsvalues"
                                size="none"
                                name="selectlistinputsvalues"
                                type="select"
                                multiple
                                label="{{ __('Select List Inputs') }}"
                                placeholder="{{ __('Enter Inputs Here') }}"
                                tooltip="{{ __('Select list inputs for the template.') }}"
                                add-new
                            >
                                @foreach ($question->selectListValues ?? [] as $input)
                                    <option value="{{ $input }}">
                                        {{ $input }}
                                    </option>
                                @endforeach
                            </x-forms.input>
                        </div>

                        <x-button
                            class="remove-inputs-group size-6 absolute -end-3 -top-3"
                            size="none"
                            variant="danger"
                            type="button"
                        >
                            <x-tabler-minus class="size-4" />
                        </x-button>
                    </x-card>
                    <div class="add-more-placeholder"></div>
                    <?php $question_i++; ?>
                @endforeach
            @else
                <x-card
                    class="user-input-group relative"
                    class:body="flex flex-col gap-5"
                    data-inputs-id="1"
                >
                    <x-forms.input
                        class="input_type"
                        id="input_type"
                        size="lg"
                        name="input_type"
                        type="select"
                        label="{{ __('Select Input Type') }}"
                        tooltip="{{ __('Input fields for short texts and Textarea fields are good for long text.') }}"
                    >
                        <option value="text">
                            {{ __('Input Field') }}
                        </option>
                        <option value="textarea">
                            {{ __('Textarea Field') }}
                        </option>
                        <option value="select">
                            {{ __('Selectlist Field') }}
                        </option>
                    </x-forms.input>

                    <x-forms.input
                        class="input_name"
                        id="input_name"
                        size="lg"
                        name="input_name"
                        label="{{ __('Input Name') }}"
                        placeholder="{{ __('Enter Name Here') }}"
                        tooltip="{{ __('Unique input name that you can use in your prompts later.') }}"
                    />

                    <x-forms.input
                        class="input_description"
                        id="input_description"
                        size="lg"
                        name="input_description"
                        label="{{ __('Input Description') }}"
                        placeholder="{{ __('Enter Description Here') }}"
                        tooltip="{{ __('A description for the input.') }}"
                    />

                    <div class="selectlistinputs hidden">
                        <x-forms.input
                            class="selectlistinputsvalues"
                            size="none"
                            name="selectlistinputsvalues"
                            type="select"
                            multiple
                            label="{{ __('Select List Inputs') }}"
                            placeholder="{{ __('Enter Inputs Here') }}"
                            tooltip="{{ __('Select list inputs for the template.') }}"
                            add-new
                        >
                        </x-forms.input>
                    </div>
                    <x-button
                        class="remove-inputs-group size-6 absolute -end-3 -top-3"
                        size="none"
                        variant="danger"
                        type="button"
                        disabled
                    >
                        <x-tabler-minus class="size-4" />
                    </x-button>
                </x-card>
                <div class="add-more-placeholder"></div>
            @endif
        </div>

        <div class="flex flex-col gap-5">
            <x-form-step
                step="3"
                label="{{ __('Prompt') }}"
            />

            <div class="flex flex-col gap-3">
                <label class="block">
                    {{ __('Created Inputs') }}
                    <x-info-tooltip text="{{ __('Click on each item to get the dynamic data from users input.') }}" />
                </label>
                <div class="after-add-more-button min-h-11 flex flex-wrap items-center gap-2 rounded-xl border border-input-border p-2">
                    @if ($template != null)
                        <?php $question_btn_i = 1; ?>
                        @foreach (json_decode($template->questions) ?? [] as $question)
                            <button
                                class="cursor-pointer rounded-full bg-heading-foreground/5 px-2 py-1 text-2xs font-medium text-heading-foreground transition-colors hover:bg-heading-foreground hover:text-heading-background"
                                data-input-name="{{ $question->question }}"
                                data-inputs-id="{{ $question_btn_i }}"
                                type="button"
                            >
                                **{{ $question->name }}**
                            </button>
                            <?php $question_btn_i++; ?>
                        @endforeach
                    @endif
                </div>
            </div>

            <x-forms.input
                id="prompt"
                size="lg"
                name="prompt"
                label="{{ __('Custom Prompt') }}"
                placeholder="{{ __('Enter Prompt Here') }}"
                type="textarea"
                rows="6"
            >{{ $template != null ? $template->prompt : null }}</x-forms.input>
        </div>

        @if ($app_is_demo)
            <x-button
                size="lg"
                onclick="return toastr.info('This feature is disabled in Demo version.')"
            >
                {{ __('Save') }}
            </x-button>
        @else
            <x-button
                id="custom_template_button"
                size="lg"
                type="submit"
            >
                {{ __('Save') }}
            </x-button>
        @endif
    </form>

    <template id="user-input-template">
        <x-card
            class="user-input-group relative"
            class:body="flex flex-col gap-5"
            data-inputs-id="1"
        >

            <x-forms.input
                class="input_type"
                id="input_type"
                size="lg"
                name="input_type"
                type="select"
                label="{{ __('Select Input Type') }}"
                tooltip="{{ __('Input fields for short texts and Textarea fields are good for long text.') }}"
            >
                <option value="text">
                    {{ __('Input Field') }}
                </option>
                <option value="textarea">
                    {{ __('Textarea Field') }}
                </option>
                <option value="select">
                    {{ __('Selectlist Field') }}
                </option>
            </x-forms.input>

            <x-forms.input
                class="input_name"
                id="input_name"
                size="lg"
                name="input_name"
                label="{{ __('Input Name') }}"
                placeholder="{{ __('Enter Name Here') }}"
                tooltip="{{ __('Unique input name that you can use in your prompts later.') }}"
            />

            <x-forms.input
                class="input_description"
                id="input_description"
                size="lg"
                name="input_description"
                label="{{ __('Input Description') }}"
                placeholder="{{ __('Enter Description Here') }}"
                tooltip="{{ __('A description for the input.') }}"
            />

            <div class="selectlistinputs hidden">
                <x-forms.input
                    class="selectlistinputsvalues"
                    size="none"
                    name="selectlistinputsvalues"
                    type="select"
                    multiple
                    label="{{ __('Select List Inputs') }}"
                    placeholder="{{ __('Enter Inputs Here') }}"
                    tooltip="{{ __('Select list inputs for the template.') }}"
                    add-new
                >
                </x-forms.input>
            </div>

            <x-button
                class="remove-inputs-group size-6 absolute -end-3 -top-3"
                size="none"
                variant="danger"
                type="button"
            >
                <x-tabler-minus class="size-4" />
            </x-button>
        </x-card>
    </template>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/openai.js') }}"></script>
    <script>
        $(document).on('change', '.input_type', function() {
            // Traverse DOM within the specific template scope
            var template = $(this).closest('.user-input-group');
            var selectlistinputs = template.find('.selectlistinputs');

            // Show or hide select list based on input_type value
            if (this.value === 'select') {
                selectlistinputs.removeClass('hidden');
            } else {
                selectlistinputs.addClass('hidden');
            }
        });
        $(document).ready(function() {
            $('.user-input-group').each(function() {
                var selectInput = $(this).find('.input_type');
                var selectListInputs = $(this).find('.selectlistinputs');

                // Function to toggle visibility of selectlistinputs
                function toggleSelectListInputs() {
                    if (selectInput.val() === 'select') {
                        selectListInputs.removeClass('hidden');
                    } else {
                        selectListInputs.addClass('hidden');
                    }
                }

                // Add change event listener to input_type select element
                selectInput.change(function() {
                    toggleSelectListInputs();
                });

                // Trigger change event initially to handle the default value
                toggleSelectListInputs();
            });
        });
    </script>
@endpush
