@php
    $base_class = 'lqd-navbar-link flex items-center gap-2 ps-navbar-link-ps pe-navbar-link-pe pt-navbar-link-pt pb-navbar-link-pb rounded-xl relative transition-colors group/link
		hover:bg-navbar-background-hover/5 hover:text-navbar-foreground-hover
		[&.active]:bg-navbar-background-active/5 [&.active]:text-navbar-foreground-active
		dark:[&.active]:bg-transparent
		dark:before:w-1.5 dark:before:h-full dark:before:absolute dark:before:top-0 dark:before:-start-2 dark:before:bg-primary dark:before:rounded-e-lg dark:before:opacity-0
		dark:[&.active]:before:opacity-100';
    $label_base_class = 'lqd-nav-link-label flex grow gap-2 items-center transition-[opacity,transform,visbility] [&_.lqd-nav-item-badge]:ms-auto';
    $letter_icon_base_class = 'lqd-nav-link-letter-icon inline-flex size-6 shrink-0 items-center justify-center rounded-md bg-primary text-4xs text-primary-foreground';
    $is_modal = $triggerType === 'modal' && !empty($modal);

    // setting href
    if (!empty($href) && $href !== '#') {
        if (is_string($href)) {
            if (!empty($slug)) {
                $href = route($href, $slug);
            } else {
                $href = route($href);
            }
        }
        if ($localizeHref) {
            $href = LaravelLocalization::localizeUrl($href);
        }
    }

    if (empty(trim($activeCondition)) && !empty($href)) {
        $activeCondition = $href === url()->current();
    }
    if ($activeCondition && !empty(trim($activeCondition))) {
        $base_class .= ' active';
    }

@endphp

@if ($is_modal)
    <x-modal type="page">
        <x-slot:trigger
            custom
        >
            @include('components.navbar.partials.link-markup')
        </x-slot:trigger>
        <x-slot:modal>
            {{ $modal }}
        </x-slot:modal>
    </x-modal>
@else
    @include('components.navbar.partials.link-markup')
@endif
