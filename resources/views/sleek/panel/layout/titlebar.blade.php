@section('titlebar_pretitle', '')
@section('titlebar_title', '')

<x-titlebar
    class="border-b-0 [&_.lqd-titlebar-subtitle]:last:mb-0"
    pretitle=""
    layout-wide="{{ isset($layout_wide) ? $layout_wide : '' }}"
    titlbar-after-place="col-nav"
>
</x-titlebar>
