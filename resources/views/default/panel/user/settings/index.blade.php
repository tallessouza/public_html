@extends('panel.layout.app')
@section('title', __('My Account'))

@section('content')
    <div class="py-10">
        <div class="container-xl">
            <div class="row">
                <div class="col-md-5 mx-auto">
                    <form
                        id="user_edit_form"
                        onsubmit="return userProfileSave();"
                        action=""
                        enctype="multipart/form-data"
                    >
                        <div class="row">
                            <div class="col-md-12 col-xl-12">

                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-[20px]">
                                            <label class="form-label">{{ __('Avatar') }}</label>
                                            <input
                                                class="form-control"
                                                id="avatar"
                                                type="file"
                                                name="avatar"
                                            >
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-[20px]">
                                            <label class="form-label">{{ __('Name') }}</label>
                                            <input
                                                class="form-control"
                                                id="name"
                                                type="text"
                                                name="name"
                                                value="{{ $user->name }}"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-[20px]">
                                            <label class="form-label">{{ __('Surname') }}</label>
                                            <input
                                                class="form-control"
                                                id="surname"
                                                type="text"
                                                name="surname"
                                                value="{{ $user->surname }}"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-[20px]">
                                            <label class="form-label">{{ __('Phone') }}</label>
                                            <input
                                                class="form-control"
                                                id="phone"
                                                data-mask="+0000000000000"
                                                data-mask-visible="true"
                                                type="text"
                                                name="phone"
                                                placeholder="+000000000000"
                                                autocomplete="off"
                                                value="{{ $user->phone }}"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-[20px]">
                                            <label class="form-label">{{ __('Email') }}</label>
                                            <input
                                                class="form-control"
                                                type="email"
                                                value="{{ $user->email }}"
                                                disabled
                                            >
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-[20px]">
                                    <label class="form-label">{{ __('Country') }}</label>
                                    <select
                                        class="form-select"
                                        id="country"
                                        type="text"
                                        name="country"
                                    >
                                        @include('panel.admin.users.countries')
                                    </select>
                                </div>

                                <div class="row">
                                    <h4>Change Password</h4>
                                    <x-alert class="!mt-2 mb-3">
                                        <p>
                                            {{ __('Please leave empty if you don’t want to change your password.') }}
                                        </p>
                                    </x-alert>
                                    <div class="col-12">
                                        <div class="mb-[20px]">
                                            <label class="form-label">{{ __('Old Password') }}</label>
                                            <input
                                                class="form-control"autocomplete="off"
                                                id="old_password"
                                                type="password"
                                                name="old_password"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-[20px]">
                                            <label class="form-label">{{ __('New Password') }}</label>
                                            <input
                                                class="form-control"autocomplete="off"
                                                id="new_password"
                                                type="password"
                                                name="new_password"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-[20px]">
                                            <label class="form-label">{{ __('Confirm Your New Password') }}</label>
                                            <input
                                                class="form-control"
                                                id="new_password_confirmation"
                                                type="password"
                                                name="new_password_confirmation"
                                                autocomplete="off"
                                            />
                                        </div>
                                    </div>
                                </div>

                                @if ($app_is_demo and Auth::user()->type == 'admin')
                                    <a
                                        class="btn btn-primary w-full"
                                        onclick="return toastr.info('Admin settings disabled on Demo version.')"
                                    >
                                        {{ __('Save') }}
                                    </a>
                                @else
                                    <button
                                        class="btn btn-primary w-full"
                                        id="user_edit_button"
                                        form="user_edit_form"
                                    >
                                        {{ __('Save') }}
                                    </button>
                                @endif

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/user.js') }}"></script>
@endpush
