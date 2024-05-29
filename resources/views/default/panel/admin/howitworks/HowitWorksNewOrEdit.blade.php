@extends('panel.layout.settings')
@section('title', isset($howitWorks) ? __('Edit Step') : __('Create New Step'))
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex flex-col gap-5"
        id="item_edit_form"
        onsubmit="return howitWorksSave({{ $howitWorks->id ?? null }});"
    >

        <x-forms.input
            id="order"
            name="order"
            label="{{ __('Order') }}"
            type="number"
            size="lg"
            value="{{ isset($howitWorks) ? $howitWorks->order : null }}"
            required
        />

        <x-forms.input
            id="title"
            name="title"
            label="{{ __('Title') }}"
            type="textarea"
            rows="10"
            size="lg"
            required
        >{{ isset($howitWorks) ? $howitWorks->title : null }}</x-forms.input>

        <x-button
            id="item_edit_button"
            size="lg"
            type="submit"
        >
            {{ __('Save') }}
        </x-button>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/howitworks.js') }}"></script>
@endpush
