<a
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
    href="{{ $href }}"
    @if ($dropdownTrigger) @click.prevent="toggleDropdownOpen()" @endif
    @if ($app_is_not_demo && ($activeCondition && !empty(trim($activeCondition)))) x-init="$el.parentElement.offsetTop > window.innerHeight && $el.closest('.lqd-navbar-inner').scrollTo({ top: (($el.parentElement.offsetHeight + $el.parentElement.offsetTop) / 2) })" @endif
    @if ($triggerType === 'modal') @click.prevent="toggleModal()" @endif
>
    @if ($letterIcon && !empty($label))
        <span {{ $attributes->twMergeFor('letter-icon', $letter_icon_base_class) }}>
            {{ mb_substr($label, 0, 1) }}
        </span>
    @endif
    @if (!empty($icon) || !empty($iconHtml))
        <span
            class="lqd-nav-link-icon bg-navbar-icon-background text-navbar-icon-foreground group-hover/link:bg-navbar-icon-background-hover group-hover/link:text-navbar-icon-foreground-hover group-[&.active]/link:bg-navbar-icon-background-active group-[&.active]/link:text-navbar-icon-foreground-active"
        >
            @if (!empty($iconHtml))
                {!! $iconHtml !!}
            @else
                <x-dynamic-component
                    class="size-navbar-icon"
                    stroke-width="1.5"
                    :component="$icon"
                />
            @endif
        </span>
    @endif

    @if (!empty($label))
        <span {{ $attributes->twMergeFor('label', $label_base_class, $attributes->get('class:label')) }}>
            {{ $label }}
        </span>
    @endif

    @if (($new && $app_is_demo) || !empty($badge))
        <x-badge
            class="ms-auto rounded-md text-4xs group-[&.navbar-shrinked]/body:hidden"
            variant="secondary"
        >
            @if ($new && $app_is_demo)
                {{ __('New') }}
            @elseif (!empty($badge))
                {{ $badge }}
            @endif
        </x-badge>
    @endif

    @if ($dropdownTrigger)
        <span class="lqd-nav-link-expander ms-auto shrink-0 group-[&.navbar-shrinked]/body:hidden">
            <x-tabler-plus
                class="w-3"
                stroke-width="2.5"
            />
        </span>
    @endif
</a>
