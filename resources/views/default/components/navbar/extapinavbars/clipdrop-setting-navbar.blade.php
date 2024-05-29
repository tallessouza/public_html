@if(\Illuminate\Support\Facades\Route::has('dashboard.admin.settings.clipdrop'))
    <x-navbar.dropdown.item>
        <x-navbar.dropdown.link
                label="{{ __('Clipdrop') }}"
                href="dashboard.admin.settings.clipdrop"
                badge="{{ trans('New') }}"
        >
        </x-navbar.dropdown.link>
    </x-navbar.dropdown.item>
@endif