@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('User Management'))
@section('titlebar_actions')
    <x-button
        href="{{ $app_is_demo ? '#' : route('dashboard.admin.users.create') }}"
        onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
        variant="primary"
    >
        <x-tabler-user-plus class="size-5" />
        {{ __('Add new user') }}
    </x-button>
@endsection
@section('titlebar_after')
    <div class="w-full md:w-1/2 lg:w-1/3">
        <form
            x-init
            x-target="users-list"
            action="/dashboard/admin/users/search"
        >
            <x-forms.input
                class="lqd-users-search-input rounded-full bg-foreground/10 ps-10 placeholder:text-foreground"
                id="search"
                name="search"
                size="sm"
                type="search"
                placeholder="{{ __('Search users') }}"
                @input.debounce="$el.form.requestSubmit()"
                @search="$el.form.requestSubmit()"
            >
                <x-slot:icon>
                    <span class="absolute start-3 top-1/2 -translate-y-1/2">
                        <x-tabler-search class="size-5" />
                    </span>
                </x-slot:icon>
            </x-forms.input>
        </form>
    </div>
@endsection

@section('content')
    <div class="py-10">
        @include('panel.admin.users.components.users-table', ['users' => $users])

        @if ($app_is_not_demo)
            <div class="mt-1 flex items-center justify-end border-t pt-4">
                <div class="m-0 ms-auto p-0">{{ $users->links() }}</div>
            </div>
        @endif
    </div>

@endsection
@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/user.js') }}"></script>
@endpush
