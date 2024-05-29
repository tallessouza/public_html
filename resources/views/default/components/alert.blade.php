@php
    $base_class = 'lqd-alert border border-input-border bg-input-background flex gap-2 font-medium [&_:first-child]:mt-0 [&_:last-child]:mb-0';
    $icon_base_class = 'lqd-alert-icon size-5 shrink-0';

    $variations = [
        'variant' => [
            'info' => 'lqd-alert-info shadow-sm text-blue-600',
            'warn' => 'lqd-alert-warn shadow-sm text-orange-600',
            'danger' => 'lqd-alert-danger shadow-sm text-red-600',
            'success' => 'lqd-alert-danger shadow-sm text-green-600',
        ],
        'size' => [
            'none' => 'lqd-alert-size-none',
            'sm' => 'lqd-alert-sm p-2 rounded-md',
            'md' => 'lqd-alert-md px-5 py-1.5 rounded-lg',
            'lg' => 'lqd-alert-lg p-6 rounded-xl',
        ],
    ];

    $variant = isset($variations['variant'][$variant]) ? $variations['variant'][$variant] : $variations['variant']['info'];
    $size = isset($variations['size'][$size]) ? $variations['size'][$size] : $variations['size']['md'];
@endphp

<div
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $variant, $size) }}
    {{ $attributes }}
>
    @if (filled($icon))
        <x-dynamic-component
            :component="$icon"
            {{ $attributes->withoutTwMergeClasses()->twMergeFor('icon', $icon_base_class) }}
        />
    @endif
    <div>
        {{ $slot }}
    </div>
</div>
