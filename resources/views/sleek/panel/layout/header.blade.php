<x-header layout-wide="{{ isset($layout_wide) ? $layout_wide : '' }}">
    <x-slot:title>
        @if (view()->hasSection('titlebar_title'))
            @yield('titlebar_title')
        @elseif (view()->hasSection('title'))
            @yield('title')
        @endif
    </x-slot:title>
    <x-slot:actions>
    </x-slot:actions>
</x-header>
