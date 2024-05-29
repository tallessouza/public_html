@extends('panel.layout.settings')
@section('title', __('Edit') . ' ' . $user->fullName())
@section('titlebar_actions', '')

@section('settings')
    <form
        id="user_edit_form"
        onsubmit="return userSave({{ $user->id }});"
        action=""
    >
        <div class="row">
            <div class="col-md-12 col-xl-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
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
                    <div class="col-md-6">
                        <div class="mb-3">
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
                    <div class="col-md-6">
                        <div class="mb-3">
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
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input
                                class="form-control"
                                id="email"
                                type="email"
                                name="email"
                                value="{{ $user->email }}"
                            >
                        </div>
                    </div>
                </div>

                <div class="mb-3">
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
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-label">{{ __('Type') }}</div>
                            <select
                                class="form-select"
                                id="type"
                                name="type"
                            >
                                <option
                                    value="admin"
                                    {{ $user->type == 'admin' ? 'selected' : '' }}
                                >
                                    {{ __('Admin') }}</option>
                                <option
                                    value="user"
                                    {{ $user->type == 'user' ? 'selected' : '' }}
                                >
                                    {{ __('User') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-label">{{ __('Status') }}</div>
                            <select
                                class="form-select"
                                id="status"
                                name="status"
                            >
                                <option
                                    value="1"
                                    {{ $user->status == 1 ? 'selected' : '' }}
                                >
                                    {{ __('Active') }}</option>
                                <option
                                    value="0"
                                    {{ $user->status == 0 ? 'selected' : '' }}
                                >
                                    {{ __('Passive') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Remaining Words') }}</label>
                            <input
                                class="form-control"
                                id="remaining_words"
                                type="number"
                                name="remaining_words"
                                value="{{ $user->remaining_words }}"
                            />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Remaining Images') }}</label>
                            <input
                                class="form-control"
                                id="remaining_images"
                                type="number"
                                name="remaining_images"
                                value="{{ $user->remaining_images }}"
                            />
                        </div>
                    </div>
                </div>

                <button
                    class="btn btn-primary w-full"
                    id="user_edit_button"
                    form="user_edit_form"
                >
                    {{ __('Save') }}
                </button>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/user.js') }}"></script>
@endpush
