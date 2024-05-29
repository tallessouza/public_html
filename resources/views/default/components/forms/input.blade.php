@php
    $container_base_class = 'lqd-input-container relative';
    $input_base_class = 'lqd-input block peer w-full px-4 py-2 border border-input-border bg-input-background text-input-foreground text-base ring-offset-0 transition-colors
		focus:border-secondary focus:outline-0 focus:ring focus:ring-secondary
		dark:focus:ring-foreground/10
		sm:text-2xs';
    $input_checkbox_base_class = 'lqd-input peer rounded border-input-border
		focus:ring focus:ring-secondary
		dark:focus:ring-foreground/10';
    $input_checkbox_custom_wrapper_base_class = 'lqd-input-checkbox-custom-wrap inline-flex items-center justify-center size-[18px] shrink-0 rounded-full bg-foreground/10 text-heading-foreground bg-center bg-no-repeat
		peer-checked:bg-primary/[7%] peer-checked:text-primary';
    $label_base_class = 'lqd-input-label flex cursor-pointer items-center gap-2 text-2xs font-medium leading-none text-label';
    $label_extra_base_class = 'ms-auto';

    $variations = [
        'size' => [
            'none' => 'lqd-input-size-none rounded-lg',
            'sm' => 'lqd-input-sm h-9 rounded-md',
            'md' => 'lqd-input-md h-10 rounded-lg',
            'lg' => 'lqd-input-lg h-11 rounded-xl',
            'xl' => 'lqd-input-xl h-14 rounded-2xl px-6',
            '2xl' => 'lqd-input-2xl h-16 rounded-full px-8',
        ],
    ];

    if ($type === 'textarea') {
        $size = 'none';
    }

    $size = isset($variations['size'][$size]) ? $variations['size'][$size] : $variations['size']['md'];

    if ($switcher) {
        $input_checkbox_base_class .= ' lqd-input-switcher w-12 h-6 border-2 border-input-border rounded-full cursor-pointer appearance-none [background-size:1.3rem] bg-left bg-no-repeat transition-all
			checked:bg-right checked:bg-heading-foreground checked:border-heading-foreground
			dark:checked:bg-label dark:checked:border-label';
    } elseif ($custom) {
        $input_checkbox_base_class = 'lqd-input peer rounded size-0 invisible absolute top-0 start-0';
    }

    if ($type !== 'checkbox' && $type !== 'radio') {
        $label_base_class .= ' mb-3';
    }

    if (!empty($label) && empty($id)) {
        $id = str()->random(10);
    }
@endphp

<div
    {{ $attributes->withoutTwMergeClasses()->twMergeFor('container', $attributes->get('class:container'), $container_base_class, $containerClass) }}
    @if ($type === 'password') x-data='{
		type: "{{ $type }}",
		get inputValueVisible() { return this.type !== "password" },
		toggleType() {
			this.type = this.type === "text" ? "password" : "text";
		}
    }' @endif
    @if ($type === 'select' && $addNew) x-data="{ 'newOptions': [] }" @endif
>
    {{-- Label --}}
    @if (!empty($label) || ($type === 'checkbox' || $type === 'radio'))
        <label
            {{ $attributes->withoutTwMergeClasses()->twMergeFor('label', $label_base_class, $attributes->get('class:label')) }}
            for={{ $id }}
        >
            {{-- Checkbox and radio --}}
            @if ($type === 'checkbox' || $type === 'radio')
                <input
                    id="{{ $id }}"
                    {{ $attributes->twMerge($input_checkbox_base_class, $attributes->get('class')) }}
                    name="{{ $name }}"
                    type={{ $type }}
                    @if ($value) value={{ $value }} @endif
                    {{ $attributes }}
                >
                @if ($custom)
                    <span {{ $attributes->twMergeFor('custom-wrap', $input_checkbox_custom_wrapper_base_class) }}></span>
                @endif
            @endif

            <span class="lqd-input-label-txt">
                {{ $label }}
            </span>

            @if ($type === 'checkbox' || $type === 'radio')
                {{ $slot }}
            @endif

            @if (!empty($labelExtra))
                <span {{ $attributes->twMergeFor('label-extra', $label_extra_base_class) }}>
                    {{ $labelExtra }}
                </span>
            @endif

            {{-- Tooltip --}}
            @if (!empty($tooltip))
                <x-info-tooltip text="{{ $tooltip }}" />
            @endif
        </label>
    @endif

    {{-- Wrapper if there is icon over the input --}}
    @if ($type === 'password' || !empty($icon) || !empty($action))
        <div class="relative">
    @endif

    {{-- Inputs other than checkbox, radio and select --}}
    @if ($type !== 'checkbox' && $type !== 'radio' && $type !== 'select' && $type !== 'textarea' && $type !== 'color')
        <input
            id="{{ $id }}"
            {{ $attributes->withoutTwMergeClasses()->twMerge($input_base_class, $size, $attributes->get('class')) }}
            name="{{ $name }}"
            value="{{ $value }}"
            @if ($type === 'password') :type="type" @endif
            type={{ $type }}
            placeholder="{{ $placeholder }}"
            {{ $attributes }}
        />

        {{ $slot }}
    @endif

    {{-- Select input --}}
    @if ($type === 'select')
        <select
            id="{{ $id }}"
            {{ $attributes->withoutTwMergeClasses()->twMerge('cursor-pointer', $input_base_class, $size, $attributes->get('class')) }}
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes }}
        >
            {{ $slot }}
            @if ($addNew)
                <template
                    x-for="option in newOptions"
                    :key="option"
                >
                    <option
                        x-text="option"
                        x-bind:value="option"
                    ></option>
                </template>
            @endif
        </select>
        @if ($attributes->has('multiple'))
            <small class="mt-1 block">
                {{ __('Hold cmd(on mac) or ctrl(on pc) to select multiple items.') }}
            </small>
        @endif
        @if ($addNew)
            <x-modal
                class:modal-backdrop="backdrop-blur-sm bg-foreground/15"
                title="{{ __('New value') }}"
            >
                <x-slot:trigger
                    class="mt-3"
                    variant="primary"
                >
                    <x-tabler-plus
                        class="size-3"
                        stroke-width="3"
                    />
                    {{ __('Add New') }}
                </x-slot:trigger>

                <x-slot:modal
                    x-data
                >
                    <x-forms.input
                        id="new_{{ $id }}"
                        @keyup.enter="$refs.submitBtn.click(); modalOpen = false"
                        name="new_{{ $name }}"
                        size="lg"
                        x-ref="new_{{ $id }}"
                    />
                    <div class="mt-4 border-t pt-3">
                        <x-button
                            @click.prevent="modalOpen = false"
                            variant="outline"
                        >
                            {{ __('Cancel') }}
                        </x-button>
                        <x-button
                            tag="button"
                            variant="primary"
                            x-ref="submitBtn"
                            @click="newOptions.push($refs.new_{{ $id }}.value); $refs.new_{{ $id }}.value = '';"
                        >
                            {{ __('Add') }}
                        </x-button>
                    </div>
                </x-slot:modal>
            </x-modal>
        @endif
    @endif

    {{-- Textarea input --}}
    @if ($type === 'textarea')
        <textarea
            id="{{ $id }}"
            {{ $attributes->withoutTwMergeClasses()->twMerge($input_base_class, $size, $attributes->get('class')) }}
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes }}
        >{{ $slot }}</textarea>
    @endif

    {{-- Color input --}}
    @if ($type === 'color')
        <div
            {{ $attributes->withoutTwMergeClasses()->twMerge($input_base_class, 'flex items-center gap-3', $size, $attributes->get('class')) }}
            x-data="{ 'colorVal': '{{ $value }}' }"
        >
            <div class="size-5 relative gap-4 overflow-hidden rounded-full border shadow-sm focus-within:ring focus-within:ring-secondary">
                <input
                    class="relative -start-1/2 -top-1/2 h-[200%] w-[200%] cursor-pointer appearance-none rounded-full border-none p-0"
                    id="{{ $id }}"
                    name="{{ $name }}"
                    value="{{ $value }}"
                    :value="colorVal"
                    type={{ $type }}
                    placeholder="{{ $placeholder }}"
                    {{ $attributes }}
                />
            </div>
            <input
                class="border-none bg-transparent text-inherit outline-none"
                id="{{ $id }}_value"
                name="{{ $name }}_value"
                value="{{ $value }}"
                type="text"
                :value="colorVal"
            />
        </div>
    @endif

    {{-- Password visibility toggle button --}}
    @if ($type === 'password')
        <button
            class="lqd-show-password size-7 absolute end-3 top-1/2 z-10 inline-flex -translate-y-1/2 cursor-pointer items-center justify-center rounded bg-none transition-colors hover:bg-foreground/10"
            type="button"
            @click="toggleType()"
        >
            <x-tabler-eye
                class="w-5"
                stroke-width="1.5"
                ::class="inputValueVisible ? 'hidden' : ''"
            />
            <x-tabler-eye-off
                class="hidden w-5"
                stroke-width="1.5"
                ::class="inputValueVisible ? '!block' : 'hidden'"
            />
        </button>
    @endif

    {{-- Icon --}}
    @if (!empty($icon))
        {!! $icon !!}
    @endif

    {{-- Action --}}
    @if (!empty($action))
        <div class="absolute inset-y-0 end-0 border-s">
            {{ $action }}
        </div>
    @endif

    {{-- Wrapper if there is icon over the input --}}
    @if ($type === 'password' || !empty($icon) || !empty($action))
</div>
@endif
</div>
