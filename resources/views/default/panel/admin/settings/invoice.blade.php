@extends('panel.layout.settings')
@section('title', __('Billing Settings'))
@section('titlebar_actions', '')

@section('settings')
    <form
        id="settings_form"
        onsubmit="return invoiceSettingsSave();"
        enctype="multipart/form-data"
    >
        <h3 class="mb-[25px] text-[20px]">{{ __('Billing Settings') }}</h3>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Invoice Name') }}</label>
                    <input
                        class="form-control"
                        id="invoice_name"
                        type="text"
                        name="invoice_name"
                        value="{{ $setting->invoice_name }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Invoice Website') }}</label>
                    <input
                        class="form-control"
                        id="invoice_website"
                        type="text"
                        name="invoice_website"
                        value="{{ $setting->invoice_website }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Invoice Address') }}</label>
                    <textarea
                        class="form-control"
                        id="invoice_address"
                        type="text"
                        name="invoice_address"
                    >{{ $setting->invoice_address }}</textarea>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('Invoice City') }}</label>
                    <input
                        class="form-control"
                        id="invoice_city"
                        type="text"
                        name="invoice_city"
                        value="{{ $setting->invoice_city }}"
                    >
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('Invoice State') }}</label>
                    <input
                        class="form-control"
                        id="invoice_state"
                        type="text"
                        name="invoice_state"
                        value="{{ $setting->invoice_state }}"
                    >
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('Invoice Postal') }}</label>
                    <input
                        class="form-control"
                        id="invoice_postal"
                        type="text"
                        name="invoice_postal"
                        value="{{ $setting->invoice_postal }}"
                    >
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('Invoice Country') }}</label>
                    <input
                        class="form-control"
                        id="invoice_country"
                        type="text"
                        name="invoice_country"
                        value="{{ $setting->invoice_country }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Invoice Phone') }}</label>
                    <input
                        class="form-control"
                        id="invoice_phone"
                        type="text"
                        name="invoice_phone"
                        value="{{ $setting->invoice_phone }}"
                    >
                </div>
            </div>

            {{-- <div class="col-md-12">
				<div class="mb-3">
					<label class="form-label">{{__('Invoice VAT')}}%</label>
					<input type="number" class="form-control" id="invoice_vat" name="invoice_vat" value="{{$setting->invoice_vat}}">
				</div>
			</div> --}}

        </div>
        <button
            class="btn btn-primary w-full"
            id="settings_button"
            form="settings_form"
        >
            {{ __('Save') }}
        </button>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
@endpush
