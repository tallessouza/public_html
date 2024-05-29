@php
    $base_class = 'lqd-navbar-label inline-block ps-navbar-link-ps ps-navbar-link-ps pt-navbar-link-pt pb-navbar-link-pb text-4xs uppercase tracking-widest
		lg:group-[&.navbar-shrinked]/body:px-2 lg:group-[&.navbar-shrinked]/body:w-full lg:group-[&.navbar-shrinked]/body:text-center';
@endphp

<span {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}>
    {{ $slot }}
</span>
