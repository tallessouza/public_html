@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', __('Workbook'))
@section('titlebar_pretitle', __('Share post your integrations.'))
@section('titlebar_title', $title)
@section('titlebar_actions', '')

@section('settings')
    <div class="py-10">
        <div class="[&_.tox-edit-area__iframe]:!bg-transparent">
            @if ($workbook->generator->type === 'code')
                <input
                    id="code_lang"
                    type="hidden"
                    value="{{ substr($workbook->input, strrpos($workbook->input, 'in') + 3) }}"
                >
                <div class="mt-4 min-h-full border-t pt-6">
                    <div
                        class="line-numbers min-h-full resize [direction:ltr] [&_kbd]:inline-flex [&_kbd]:rounded [&_kbd]:bg-primary/10 [&_kbd]:px-1 [&_kbd]:py-0.5 [&_kbd]:font-semibold [&_kbd]:text-primary [&_pre[class*=language]]:my-4 [&_pre[class*=language]]:rounded"
                        id="code-pre"
                    ><code id="code-output">{{ $workbook->output }}</code></div>
                </div>
            @elseif($workbook->generator->type === 'image')
                <figure>
                    <a href="{{ $workbook->output }}">
                        <img
                            class="rounded-xl shadow-xl"
                            src="{{ custom_theme_url($workbook->output) }}"
                            alt="{{ __($workbook->generator->title) }}"
                        />
                    </a>
                </figure>
            @elseif(in_array($workbook->generator->type, ['text', 'youtube', 'rss', 'audio']))
                <form
                    class="workbook-form group/form flex flex-col gap-6"
                    method="POST"
                    action="{{ route('dashboard.user.integration.share.workbook', [$userIntegration->id, $workbook->id]) }}"
                >
                    @csrf
                    <x-forms.input
                        class="border-transparent font-serif text-2xl"
                        id="workbook_title"
                        name="title"
                        placeholder="{{ __('Untitled Document...') }}"
                        value="{{ $workbook->title }}"
                    />
                    <x-forms.input
                        class="tinymce border-0 font-body"
                        id="content"
                        name="workbook_text"
                        type="textarea"
                        rows="25"
                    >{!! $workbook->output !!}</x-forms.input>
                    <x-button
                        class="w-full"
                        id="share"
                        tag="button"
                        type="submit"
                        variant="primary"
                        size="lg"
                    >
                        <span class="group-[&.loading]/form:hidden">{{ __('Share') }}</span>
                        <span class="hidden group-[&.loading]/form:inline-block">{{ __('Please wait...') }}</span>
                    </x-button>
                </form>
            @endif
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/libs/beautify-html.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/ace/src-min-noconflict/ace.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/ace/src-min-noconflict/ext-language_tools.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/markdown-it.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/turndown.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/panel/tinymce-theme-handler.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/panel/workbook.js') }}"></script>

    @if ($openai->type == 'code')
        <link
            rel="stylesheet"
            href="{{ custom_theme_url('/assets/libs/prism/prism.css') }}"
        />
        <script src="{{ custom_theme_url('/assets/libs/prism/prism.js') }}"></script>
        <script>
            window.Prism = window.Prism || {};
            window.Prism.manual = true;
            document.addEventListener('DOMContentLoaded', (event) => {
                "use strict";

                const codeLang = document.querySelector('#code_lang');
                const codePre = document.querySelector('#code-pre');
                const codeOutput = codePre?.querySelector('#code-output');

                if (!codeOutput) return;

                codePre.classList.add(`language-${codeLang && codeLang.value !== '' ? codeLang.value : 'javascript'}`);

                // saving for copy
                window.codeRaw = codeOutput.innerText;

                Prism.highlightElement(codeOutput);
            });
        </script>
    @endif
@endpush
