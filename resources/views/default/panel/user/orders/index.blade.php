@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('My Orders'))
@section('titlebar_actions', '')
@section('content')
    <!-- Page body -->
    <div class="py-10">
        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Order Id') }}
                    </th>
                    <th>
                        {{ __('Status') }}
                    </th>
                    <th>
                        {{ __('Plan Name') }}
                    </th>
                    <th>
                        {{ __('Words') }}
                    </th>
                    <th>
                        {{ __('Images') }}
                    </th>
                    <th>
                        {{ __('Price') }}
                    </th>
                    <th>
                        {{ __('Tax') }}
                    </th>
                    <th>
                        {{ __('Type') }}
                    </th>
                    <th>
                        {{ __('Date') }}
                    </th>
                    <th class="text-end">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach ($list as $entry)
                    <tr>
                        <td>
                            {{ $entry->order_id }}
                        </td>
                        @php
                            switch ($entry->status) {
                                case 'Success':
                                    $badge_type = 'success';
                                    break;
                                case 'Waiting':
                                    $badge_type = 'secondary';
                                    break;
                                case 'Approved':
                                    $badge_type = 'success';
                                    break;
                                case 'Rejected':
                                    $badge_type = 'danger';
                                    break;
                                default:
                                    $badge_type = 'default';
                                    break;
                            }
                        @endphp
                        <td>
                            <x-badge
                                class="text-[12px]"
                                variant="{{ $badge_type }}"
                            >
                                {{ __($entry->status) }}
                            </x-badge>
                        </td>
                        <td>
                            {{ __(@$entry->plan->name ?? 'Archived') }}
                        </td>
                        <td>
                            {{ @$entry->plan->total_words ?? '-' }}
                        </td>
                        <td>
                            {{ @$entry->plan->total_images ?? '-' }}
                        </td>
                        <td>
                            @if (currencyShouldDisplayOnRight(currency()->symbol))
                                {{ $entry->price }}{{ currency()->symbol }}
                            @else
                                {{ currency()->symbol }}{{ $entry->price }}
                            @endif
                        </td>
                        <td>
                            @if (currencyShouldDisplayOnRight(currency()->symbol))
                                {{ $entry->tax_value ?? 0 }}{{ currency()->symbol }}
                            @else
                                {{ currency()->symbol }}{{ $entry->tax_value ?? 0 }}
                            @endif
                        </td>
                        <td>
                            {{ __($entry->type) }}
                        </td>
                        <td>
                            <p class="m-0">
                                {{ date('j.n.Y', strtotime($entry->created_at)) }}
                                <span class="block opacity-60">
                                    {{ date('H:i:s', strtotime($entry->created_at)) }}
                                </span>
                            </p>
                        </td>
                        <td class="whitespace-nowrap text-end">
                            <x-button
                                class="size-9"
                                href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.orders.invoice', $entry->order_id)) }}"
                                size="none"
                                variant="ghost-shadow"
                                title="{{ __('Invoice') }}"
                            >
                                <x-tabler-news class="size-5" />
                            </x-button>
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-table>
    </div>
@endsection
