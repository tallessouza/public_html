@if (setting('photo_studio', 1) == 1)
    <x-navbar.item>
        <x-navbar.link
                label="{{ __('AI Photo Studio') }}"
                href="dashboard.user.photo-studio.index"
                icon="tabler-camera-check"
                badge="{{ trans('New') }}"
        />
    </x-navbar.item>
@endif