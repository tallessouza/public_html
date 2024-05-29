@extends('panel.layout.settings')
@section('title', __('Affiliate Settings'))
@section('titlebar_actions', '')

@section('settings')
    <form
        id="settings_form"
        onsubmit="return affiliateSettingsSave();"
        enctype="multipart/form-data"
    >
        <div class="mb-[25px]">
            <label class="form-label">{{ __('Affiliate Minimum Withdrawal') }}</label>
            <input
                class="form-control"
                id="affiliate_minimum_withdrawal"
                type="number"
                name="affiliate_minimum_withdrawal"
                value="{{ $setting->affiliate_minimum_withdrawal }}"
            >
        </div>
        <div class="mb-[25px]">
            <label class="form-label">{{ __('Affiliate Comission Percentage') }} (%)</label>
            <input
                class="form-control"
                id="affiliate_commission_percentage"
                type="number"
                name="affiliate_commission_percentage"
                value="{{ $setting->affiliate_commission_percentage }}"
            >
        </div>
        <button
            class="btn btn-primary w-full"
            id="settings_button"
            form="settings_form"
        >
            {{ __('Save') }}
        </button>
    </form>
    @includeIf('panel.admin.settings.particles.affiliate-setting')
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
    @includeIf('panel.admin.settings.particles.affiliate-setting-script')
@endpush
