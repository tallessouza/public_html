@extends('panel.layout.settings', ['layout' => 'wide'])
@section('title', __('Send email'))
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex flex-col gap-5"
        id="form-submit"
        action="{{ route('dashboard.email-templates.send', $template->id) }}"
        enctype="multipart/form-data"
        method="post"
    >
        @csrf
        <x-alert>
            <p>
                @lang("If you want your email sending to be efficient, you should use Redis for the queue. If your queue is sync, you won't benefit from this process.")
            </p>

            <span>
                @lang("You can separate email addresses with the following symbols in English: ',', '\n' (newline), '\r' (carriage return), ';', ' ', '|'")
            </span>
        </x-alert>

        <div class="space-y-2">
            <x-forms.input
                id="receivers"
                type="textarea"
                size="lg"
                label="{{ __('Receiver') }}"
                tooltip="{{ __('Please only include users available in the system, and if you have used {user_name} in the template, you should be mindful of this.') }}"
                rows="10"
                name="receivers"
            >{{ old('receivers') }}</x-forms.input>
            @error('receiver')
                <p class="text-red-500">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <x-forms.input
            id="flexCheckDefault"
            name="all_customers"
            :checked="old('all_customers')"
            type="checkbox"
            label="{{ __('All customers') }}"
            switcher
            type="checkbox"
        />

        <x-button
            id="email_templates_button"
            size="lg"
            type="submit"
        >
            {{ __('Send') }}
        </x-button>
    </form>
@endsection
