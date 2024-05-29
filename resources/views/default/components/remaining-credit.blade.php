@php
    $base_class = 'lqd-remaining-credit flex flex-col gap-2';
    $progress_base_class = 'lqd-progress flex h-2 overflow-hidden rounded-full';
    $progressbar_text_base_class = 'lqd-progress-bar shrink-0 grow-0 basis-auto bg-primary';
    $progressbar_image_base_class = 'lqd-progress-bar shrink-0 grow-0 basis-auto bg-secondary';
    $legend_text_base_class = '';
    $legend_box_text_base_class = '';
    $legend_image_base_class = '';
    $legend_box_image_base_class = 'bg-secondary';

    $variations = [
        'progressHeight' => [
            'sm' => 'h-1',
            'md' => 'h-2',
        ],
    ];

    $progressHeight = isset($variations['progressHeight'][$progressHeight]) ? $variations['progressHeight'][$progressHeight] : $variations['progressHeight']['md'];
@endphp

<div {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}>
    <div @class([
        'flex items-center justify-between gap-3 flex-wrap' => $style === 'inline',
    ])>
        <x-legend
            class="{{ @twMerge($legend_text_base_class, $attributes->get('class:legend-text')) }}"
            class:box="{{ @twMerge($legend_box_text_base_class, $attributes->get('class:legend-text-box')) }}"
            size="{{ $legendSize }}"
            label="{{ __($labelWords) }}"
        >
            <span class="ms-auto font-medium">
                {{ (int) Auth::user()->remaining_words != -1 ? number_format((int) Auth::user()->remaining_words) : __('Unlimited') }}
            </span>
        </x-legend>
        <x-legend
            class="{{ @twMerge($legend_image_base_class, $attributes->get('class:legend-image')) }}"
            class:box="{{ @twMerge($legend_box_image_base_class, $attributes->get('class:legend-image-box')) }}"
            size="{{ $legendSize }}"
            label="{{ __($labelImages) }}"
        >
            <span class="ms-auto font-medium">
                {{ (int) Auth::user()->remaining_images != -1 ? number_format((int) Auth::user()->remaining_images) : __('Unlimited') }}
            </span>
        </x-legend>
    </div>
    <div {{ $attributes->twMergeFor('progress', $progress_base_class, $progressHeight) }}>
        @if ((int) Auth::user()->remaining_words + (int) Auth::user()->remaining_images != 0)
            <div
                {{ $attributes->twMergeFor('progressbar-text', $progressbar_text_base_class) }}
                style="width: {{ ((int) Auth::user()->remaining_words / ((int) Auth::user()->remaining_words + (int) Auth::user()->remaining_images)) * 100 }}%"
            ></div>
        @endif
        @if ((int) Auth::user()->remaining_words + (int) Auth::user()->remaining_images != 0)
            <div
                {{ $attributes->twMergeFor('progressbar-image', $progressbar_image_base_class) }}
                style="width: {{ ((int) Auth::user()->remaining_images / ((int) Auth::user()->remaining_words + (int) Auth::user()->remaining_images)) * 100 }}%"
            ></div>
        @endif
    </div>
</div>
