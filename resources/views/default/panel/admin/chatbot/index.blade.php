@extends('panel.layout.app', ['disable_tblr' => true])

@section('title', $title)
@section('titlebar_subtitle', __('Train MagicAI on your own data (website or PDF) and make your AI content exclusive.'))
@section('titlebar_actions')
    <div class="space-x-1.5">
        <x-button
            variant="primary"
            onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
            href="{{ $app_is_demo ? '#' : LaravelLocalization::localizeUrl(route('dashboard.admin.chatbot.create')) }}"
        >
            {{ __('Add New Chatbot') }}
        </x-button>
    </div>
@endsection

@section('content')
    <div class="py-10">
        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Name') }}
                    </th>
                    <th>
                        {{ __('Status') }}
                    </th>
                    <th>
                        {{ __('User') }}
                    </th>
                    <th>
                        {{ __('Created') }}
                    </th>
                    <th>
                        {{ __('Model') }}
                    </th>
                    <th class="text-end">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach ($items as $item)
                    <tr>
                        <td>
                            @if ($item->image)
                                <img
                                    class="size-12 me-2 rounded-full object-cover object-center shadow-lg"
                                    src="{{ $item->image_url }}"
                                    alt="{{ $item->title }}"
                                >
                            @else
                                <div
                                    class="size-[60px] border-bg mx-auto me-2 inline-flex items-center justify-center truncate rounded-full bg-secondary text-center text-xl font-medium shadow-lg transition-shadow">
                                    <span class="block w-full truncate">
                                        {{ \Illuminate\Support\Str::limit($item->title, 2, '') }}
                                    </span>
                                </div>
                            @endif
                            {{ $item->title }}
                        </td>
                        <td>
                            @include('panel.admin.chatbot.status', ['status' => $item->status])
                        </td>
                        <td>
                            {{ $item->user?->name ?: trans('System') }}
                        </td>
                        <td>
                            <p class="m-0">
                                <span class="block opacity-60">
                                    {{ $item?->created_at?->diffForHumans() }}
                                </span>
                            </p>
                        </td>
                        <td>
                            <span class="uppercase text-primary">
                                {{ $item->model }}
                            </span>
                        </td>
                        <td class="text-end">
                            <x-button
                                class="size-9"
                                variant="ghost-shadow"
                                size="none"
                                href="{{ route('dashboard.admin.chatbot.show', $item->id) }}"
                                title="{{ __('Chatbot Training') }}"
                            >
                                {{-- blade-formatter-disable --}}
								<svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" > <path fill-rule="evenodd" clip-rule="evenodd" d="M16.1681 6.15216L14.7761 6.43416V6.43616C14.1057 6.57221 13.4902 6.90274 13.0064 7.38647C12.5227 7.87021 12.1922 8.48572 12.0561 9.15617L11.7741 10.5482C11.7443 10.6852 11.6686 10.8079 11.5594 10.8958C11.4503 10.9838 11.3143 11.0318 11.1741 11.0318C11.0339 11.0318 10.8979 10.9838 10.7888 10.8958C10.6796 10.8079 10.6039 10.6852 10.5741 10.5482L10.2921 9.15617C10.1563 8.48561 9.82586 7.86997 9.34209 7.38619C8.85831 6.90241 8.24266 6.57197 7.57211 6.43616L6.18011 6.15416C6.0413 6.12574 5.91656 6.05026 5.82698 5.94048C5.7374 5.8307 5.68848 5.69336 5.68848 5.55166C5.68848 5.40997 5.7374 5.27263 5.82698 5.16285C5.91656 5.05307 6.0413 4.97759 6.18011 4.94916L7.57211 4.66716C8.24261 4.53124 8.85819 4.20076 9.34195 3.717C9.8257 3.23324 10.1562 2.61766 10.2921 1.94716L10.5741 0.555164C10.6039 0.418164 10.6796 0.295476 10.7888 0.207494C10.8979 0.119512 11.0339 0.0715332 11.1741 0.0715332C11.3143 0.0715332 11.4503 0.119512 11.5594 0.207494C11.6686 0.295476 11.7443 0.418164 11.7741 0.555164L12.0561 1.94716C12.1922 2.61761 12.5227 3.23312 13.0064 3.71686C13.4902 4.20059 14.1057 4.53112 14.7761 4.66716L16.1681 4.94716C16.3069 4.97559 16.4317 5.05107 16.5212 5.16085C16.6108 5.27063 16.6597 5.40797 16.6597 5.54966C16.6597 5.69136 16.6108 5.8287 16.5212 5.93848C16.4317 6.04826 16.3069 6.12374 16.1681 6.15216ZM5.98931 13.2052L5.61131 13.2822C5.14508 13.3767 4.71703 13.6055 4.38056 13.9418C4.04409 14.2781 3.81411 14.706 3.71931 15.1722L3.64231 15.5502C3.62171 15.6567 3.56468 15.7527 3.48102 15.8217C3.39735 15.8907 3.29227 15.9285 3.18381 15.9285C3.07534 15.9285 2.97026 15.8907 2.88659 15.8217C2.80293 15.7527 2.74591 15.6567 2.72531 15.5502L2.6483 15.1722C2.55362 14.7059 2.32368 14.2779 1.98719 13.9416C1.6507 13.6053 1.22258 13.3756 0.756305 13.2812L0.378305 13.2042C0.271814 13.1836 0.175815 13.1265 0.106785 13.0429C0.037755 12.9592 0 12.8541 0 12.7457C0 12.6372 0.037755 12.5321 0.106785 12.4485C0.175815 12.3648 0.271814 12.3078 0.378305 12.2872L0.756305 12.2102C1.22271 12.1157 1.65093 11.8858 1.98743 11.5493C2.32393 11.2128 2.5538 10.7846 2.6483 10.3182L2.72531 9.94016C2.74591 9.83367 2.80293 9.73767 2.88659 9.66864C2.97026 9.59961 3.07534 9.56186 3.18381 9.56186C3.29227 9.56186 3.39735 9.59961 3.48102 9.66864C3.56468 9.73767 3.62171 9.83367 3.64231 9.94016L3.71931 10.3182C3.81376 10.7847 4.04359 11.2131 4.38008 11.5497C4.71658 11.8864 5.14482 12.1165 5.61131 12.2112L5.98931 12.2882C6.0958 12.3088 6.1918 12.3658 6.26083 12.4495C6.32985 12.5331 6.36761 12.6382 6.36761 12.7467C6.36761 12.8551 6.32985 12.9602 6.26083 13.0439C6.1918 13.1275 6.0958 13.1846 5.98931 13.2052Z" fill="url(#paint0_linear_3314_1636)" ></path> <defs> <linearGradient id="paint0_linear_3314_1636" x1="1.03221e-07" y1="3.30635" x2="13.3702" y2="15.6959" gradientUnits="userSpaceOnUse" > <stop stop-color="#82E2F4"></stop> <stop offset="0.502" stop-color="#8A8AED" ></stop> <stop offset="1" stop-color="#6977DE" ></stop> </linearGradient> </defs> </svg>
								{{-- blade-formatter-enable --}}
                            </x-button>
                            <x-button
                                class="size-9"
                                variant="ghost-shadow"
                                hover-variant="danger"
                                size="none"
                                onclick="deleteChatbot('{{ route('dashboard.admin.chatbot.destroy', $item->id) }}')"
                                title="{{ __('Delete') }}"
                            >
                                <x-tabler-x class="size-4" />
                            </x-button>
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-table>

        @if ($items->total() > 10)
            <div class="mt-1 flex items-center justify-end border-t pt-4">
                {{ $items->links() }}
            </div>
        @endif
    </div>
@endsection
@push('script')
    <script>
        function deleteChatbot(url) {
            if (confirm('{{ __('Are you sure you want to delete the chatbot?') }}')) {
                $.ajax({
                    type: "DELETE",
                    url: url,
                    success: function(result) {
                        if (result.status == 'error') {
                            toastr.error(result.message);
                        } else {
                            toastr.success(result.message);
                            if (result.reload) {
                                setTimeout(function() {
                                    location.reload();
                                }, result.setTimeOut);
                            }
                        }
                    }
                });
            }
        }
    </script>
@endpush
