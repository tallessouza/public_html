@extends('panel.layout.app')
@section('title', __('Update'))
@section('titlebar_actions', '')
@section('content')
    <div class="py-10">
        <div class="container-xl">
            @if (Auth::user()->type == 'admin')
                <div class="col-12 md:px-10">
                    @if ($app_is_not_demo)

                        @if (getServerMemoryLimit() < 64 && getServerMemoryLimit() != -1)
                            <div class="card mb-12 bg-red-100 shadow-sm">
                                <svg
                                    class="absolute end-7 top-7 text-red-500 dark:text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="40"
                                    height="40"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    fill="none"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        stroke="none"
                                        d="M0 0h24v24H0z"
                                        fill="none"
                                    ></path>
                                    <path d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z">
                                    </path>
                                    <path d="M12 9v4"></path>
                                    <path d="M12 17h.01"></path>
                                </svg>
                                <div class="card-body p-10">
                                    <div class="row">
                                        <div class="col-12">
                                            <h4 class="mb-6 w-10/12 text-[23px] leading-[1.3em]">{{ __('Warning') }}</h4>
                                            <p class="mb-1.5">{{ __('Your server memory_limit is') }}
                                                <strong>{{ getServerMemoryLimit() }}M</strong>.
                                                {{ __('It should be minimum') }} <strong>64M</strong>.
                                            </p>
                                            <p>{{ __('If you do not set the required minimum value, you may get possible errors during the update.') }}
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif

                        <div
                            class="card mb-12 hidden bg-[#F3E2FD] shadow-sm"
                            id="update_card"
                        >
                            <svg
                                class="absolute end-7 top-7 text-[#330582] dark:text-white"
                                width="41"
                                height="41"
                                viewBox="0 0 41 41"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M30.605 16.8863L19.0491 28.4421C18.2529 29.2384 16.9666 29.2384 16.1704 28.4421L10.3924 22.6642C9.5962 21.868 9.5962 20.5817 10.3924 19.7855C11.1887 18.9892 12.4749 18.9892 13.2712 19.7855L17.5995 24.1138L27.7058 14.0076C28.502 13.2113 29.7883 13.2113 30.5845 14.0076C31.4012 14.8038 31.4012 16.0901 30.605 16.8863ZM4.16536 20.5001C4.16536 15.743 6.24786 11.4759 9.51453 8.49508L12.6383 11.6188C13.2712 12.2517 14.3737 11.8026 14.3737 10.8838V2.12508C14.3737 1.55341 13.9245 1.10424 13.3529 1.10424H4.59411C3.67536 1.10424 3.2262 2.20674 3.87953 2.83966L6.61536 5.59591C2.6137 9.31174 0.0820312 14.5997 0.0820312 20.5001C0.0820312 30.198 6.86037 38.3238 15.9254 40.4063C17.2116 40.6921 18.457 39.7326 18.457 38.4055C18.457 37.4459 17.7833 36.6292 16.8441 36.4046C9.5962 34.7509 4.16536 28.2584 4.16536 20.5001ZM40.9154 20.5001C40.9154 10.8022 34.137 2.67633 25.072 0.593828C23.7858 0.307995 22.5404 1.26758 22.5404 2.59466C22.5404 3.55424 23.2141 4.37091 24.1533 4.59549C31.4012 6.24925 36.832 12.7417 36.832 20.5001C36.832 25.2571 34.7495 29.5242 31.4829 32.5051L28.3591 29.3813C27.7262 28.7484 26.6237 29.1976 26.6237 30.1163V38.8751C26.6237 39.4467 27.0729 39.8959 27.6445 39.8959H36.4033C37.322 39.8959 37.7712 38.7934 37.1179 38.1605L34.382 35.4042C38.3837 31.6884 40.9154 26.4005 40.9154 20.5001Z"
                                />
                            </svg>
                            <div class="card-body p-10">
                                <div class="row">
                                    <div class="col-12 col-md-5 max-w-[390px]">
                                        <h4 class="text-heading mb-6 w-10/12 text-[23px] leading-[1.3em]">
                                            {{ __('A newer version of') }} {{ $setting->site_name }}
                                            {{ __('is available.') }}</h4>
                                        <p class="text-heading mb-3 text-[18px] font-semibold"><strong id="update_version">{{ $setting->script_version }}</strong></p>
                                        @if (getServerMemoryLimit() < 64 && getServerMemoryLimit() != -1)
                                            <button
                                                class="btn min-w-[90%] cursor-not-allowed rounded-full !py-[0.65rem] px-10 text-[15px] font-medium opacity-80"
                                                type="button"
                                            >
                                                {{ __('Update the memory_limit value') }}
                                            </button>
                                        @else
                                            <button
                                                class="btn min-w-[90%] rounded-full !py-[0.65rem] px-10 text-[15px] font-medium"
                                                id="update_btn"
                                                type="button"
                                                onclick="update();"
                                            >
                                                {{ __('Update Now') }}
                                                <svg
                                                    class="!me-1 rtl:-scale-100"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="19"
                                                    height="19"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                    fill="none"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                >
                                                    <path d="M9 6l6 6l-6 6"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endif
                    <div
                        class="card mb-12 hidden bg-green-100 shadow-sm"
                        id="update_not_available"
                        style="display: none;"
                    >
                        <svg
                            class="absolute end-7 top-7 text-green-600 dark:text-white"
                            width="41"
                            height="41"
                            viewBox="0 0 41 41"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M30.605 16.8863L19.0491 28.4421C18.2529 29.2384 16.9666 29.2384 16.1704 28.4421L10.3924 22.6642C9.5962 21.868 9.5962 20.5817 10.3924 19.7855C11.1887 18.9892 12.4749 18.9892 13.2712 19.7855L17.5995 24.1138L27.7058 14.0076C28.502 13.2113 29.7883 13.2113 30.5845 14.0076C31.4012 14.8038 31.4012 16.0901 30.605 16.8863ZM4.16536 20.5001C4.16536 15.743 6.24786 11.4759 9.51453 8.49508L12.6383 11.6188C13.2712 12.2517 14.3737 11.8026 14.3737 10.8838V2.12508C14.3737 1.55341 13.9245 1.10424 13.3529 1.10424H4.59411C3.67536 1.10424 3.2262 2.20674 3.87953 2.83966L6.61536 5.59591C2.6137 9.31174 0.0820312 14.5997 0.0820312 20.5001C0.0820312 30.198 6.86037 38.3238 15.9254 40.4063C17.2116 40.6921 18.457 39.7326 18.457 38.4055C18.457 37.4459 17.7833 36.6292 16.8441 36.4046C9.5962 34.7509 4.16536 28.2584 4.16536 20.5001ZM40.9154 20.5001C40.9154 10.8022 34.137 2.67633 25.072 0.593828C23.7858 0.307995 22.5404 1.26758 22.5404 2.59466C22.5404 3.55424 23.2141 4.37091 24.1533 4.59549C31.4012 6.24925 36.832 12.7417 36.832 20.5001C36.832 25.2571 34.7495 29.5242 31.4829 32.5051L28.3591 29.3813C27.7262 28.7484 26.6237 29.1976 26.6237 30.1163V38.8751C26.6237 39.4467 27.0729 39.8959 27.6445 39.8959H36.4033C37.322 39.8959 37.7712 38.7934 37.1179 38.1605L34.382 35.4042C38.3837 31.6884 40.9154 26.4005 40.9154 20.5001Z"
                            />
                        </svg>
                        <div class="card-body p-10">
                            <h2>{{ $setting->site_name }} {{ __('is up to date') }}</h2>
                        </div>
                    </div>
                    <p class="text-heading text-[18px] font-semibold">{{ __('Change-log') }}</p>
                    <hr class="mb-4">
                    <div id="update_description"></div>
                    <p class="update_status"></p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('script')
    @auth()
        <script>
            @if (\Illuminate\Support\Facades\Auth::user()->type == 'admin')
                $(document).ready(function() {
                    $.ajax({
                        type: 'GET',
                        url: '/magicai.updater.check',
                        async: false,
                        success: function(response) {
                            if (response.update) {
                                $('#update_version').html('Version ' + response.version_format);
                                let badgeHtml =
                                    '<span class="lqd-nav-link-badge text-xs rounded-full flex ms-auto justify-center items-center bg-[#F3E2FD] text-black !h-5 !w-5">1</span>';
                                localStorage.setItem("magicai_update_badge", badgeHtml);

                                $('#update_available').show();
                                $('#update_card').show();
                            } else {
                                $('#update_not_available').show();
                                localStorage.removeItem("magicai_update_badge");
                            }

                            if (response.description) {
                                changelog(response.description);
                            } else {
                                $('#update_description').html(
                                    '<a href="https://magicaidocs.liquid-themes.com/changelog/" target="_blank">Visit Changelog on the Documentation Page</a>'
                                );
                            }
                        }
                    });
                });


                function update() {
                    let confirmation = confirm(
                        'Please do not forget to backup whole system and database. Liquid-themes team does not take any responsibility.'
                    );
                    if (confirmation == false) {
                        return false;
                    }
                    $("#update_btn").html('Updating');
                    $("#update_btn").disabled = true;
                    $.ajax({
                        type: 'GET',
                        url: '/magicai.updater.update',
                        success: function(response) {
                            if (response != '') {
                                $('#update_status').html(response);
                                $("#update_btn").html('Update Completed!');
                                $("#update_btn").attr("onclick", "");
                                localStorage.removeItem("magicai_update_badge");
                            }
                        },
                        error: function(response) {
                            if (response != '') {
                                $('#update_status').html(response);
                                $("#update_btn").html('Error, Please try again');
                                $("#update_btn").disabled = false;
                            }
                        }
                    });
                }

                function changelog(response) {
                    var descriptionHTML = '';
                    for (var version in response) {
                        descriptionHTML += '<p class="mb-6">';
                        descriptionHTML +=
                            '<span class="inline-block px-2 py-1 rounded-md text-heading bg-[#f7f7f7] font-semibold dark:bg-white dark:bg-opacity-10">Version ' +
                            version + '</span>';
                        descriptionHTML += '</p>';
                        descriptionHTML += '<ul class="list-none p-0 m-0">';

                        for (var i = 0; i < response[version].length; i++) {
                            var type = Object.keys(response[version][i])[0];
                            var message = response[version][i][type];

                            descriptionHTML += '<li class="mb-[20px]">';
                            descriptionHTML +=
                                '<span class="inline-block min-w-[60px] px-2 py-[0.15rem] me-3 rounded-full uppercase text-[10px] font-bold text-center ';

                            if (type === 'New') {
                                descriptionHTML += 'bg-green-100 text-green-600">new';
                            } else if (type === 'Fix') {
                                descriptionHTML += 'bg-red-100 text-red-600">fix';
                            } else if (type === 'Tweak') {
                                descriptionHTML += 'bg-blue-100 text-blue-600">tweak';
                            } else {
                                descriptionHTML += 'bg-yellow-100 text-yellow-600">' + type;
                            }

                            descriptionHTML += '</span>';
                            descriptionHTML += message;
                            descriptionHTML += '</li>';
                        }

                        descriptionHTML += '</ul>';
                        descriptionHTML += '<hr class="my-4">';
                    }

                    $('#update_description').html(descriptionHTML);
                }
            @endif
        </script>
    @endauth
@endpush
