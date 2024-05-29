@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Workbook'))
@section('titlebar_pretitle', __('Edit your generations.'))
@section('titlebar_title', $workbook->title)
@section('titlebar_actions_after')
    <div class="flex items-center gap-4 max-lg:hidden lg:ms-4">
        <x-dropdown.dropdown
            class="doc-share-dropdown"
            class:dropdown-dropdown="max-lg:end-auto max-lg:start-0"
            anchor="end"
            offsetY="20px"
        >
            <x-slot:trigger>
                {{ __('Share') }}
                <span
                    class="size-6 inline-grid shrink-0 place-items-center rounded-md bg-foreground/10 transition-all group-hover/dropdown:scale-105 group-hover/dropdown:bg-heading-foreground group-hover/dropdown:text-heading-background"
                >
                    <x-tabler-share class="size-4" />
                </span>
            </x-slot:trigger>
            <x-slot:dropdown
                class="py-1 text-2xs"
            >
                <x-button
                    class="w-full justify-start rounded-none px-3 py-2 text-start hover:bg-heading-foreground/5"
                    variant="link"
                    target="_blank"
                    href="http://twitter.com/share?text={{ $workbook->output }}"
                >
                    <x-tabler-brand-x />
                    @lang('X')
                </x-button>
                <x-button
                    class="w-full justify-start rounded-none px-3 py-2 text-start hover:bg-heading-foreground/5"
                    variant="link"
                    target="_blank"
                    href="https://wa.me/?text={{ htmlspecialchars($workbook->output) }}"
                >
                    <x-tabler-brand-whatsapp />
                    @lang('Whatsapp')
                </x-button>
                <x-button
                    class="w-full justify-start rounded-none px-3 py-2 text-start hover:bg-heading-foreground/5"
                    variant="link"
                    target="_blank"
                    href="https://t.me/share/url?url={{ request()->host() }}&text={{ htmlspecialchars($workbook->output) }}"
                >
                    <x-tabler-brand-telegram />
                    @lang('Telegram')
                </x-button>
            </x-slot:dropdown>
        </x-dropdown.dropdown>

        @if (!empty($integrations) && $checkIntegration)
            <x-dropdown.dropdown
                class="doc-integrate-publish-dropdown"
                class:dropdown-dropdown="max-lg:end-auto max-lg:start-0"
                anchor="end"
                offsetY="20px"
            >
                <x-slot:trigger
                    variant="success"
                >
                    {{ __('Publish') }}
                </x-slot:trigger>
                <x-slot:dropdown
                    class="min-w-48 text-xs"
                >
                    <p class="border-b px-3 py-3 text-foreground/70">
                        @lang('Integrations')
                    </p>
                    <div class="pb-2">
                        @foreach ($integrations as $integration)
                            <x-button
                                class="w-full justify-start rounded-none px-3 py-2 text-start hover:bg-heading-foreground/5"
                                variant="link"
                                href="{{ route('dashboard.user.integration.share.workbook', [$integration->id, $workbook->id]) }}"
                            >
                                {{ $integration?->integration?->app }}
                            </x-button>
                        @endforeach

                    </div>
                </x-slot:dropdown>
            </x-dropdown.dropdown>
        @endif
    </div>
@endsection

@section('content')
    <div class="py-10">
        <div class="mx-auto w-full lg:w-3/5">
            @include('panel.user.openai.documents_workbook_textarea')
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

                if (codeOutput) {
                    let codeOutputText = codeOutput.textContent;
                    const codeBlocks = codeOutputText.match(/```[A-Za-z_]*\n[\s\S]+?```/g);
                    if (codeBlocks) {
                        codeBlocks.forEach((block) => {
                            const language = block.match(/```([A-Za-z_]*)/)[1];
                    const code = block.replace(/```[A-Za-z_]*\n/, '').replace(/```/, '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(
                        /"/g, '&quot;').replace(/'/g, '&#039;');
                    const wrappedCode = `<pre><code class="language-${language}">${code}</code></pre>`;
                    codeOutputText = codeOutputText.replace(block, wrappedCode);
                });
            }

            codePre.innerHTML = codeOutputText;

            codePre.querySelectorAll('pre').forEach(pre => {
                pre.classList.add(`language-${codeLang && codeLang.value !== '' ? codeLang.value : 'javascript'}`);
                    })

                    // saving for copy
                    window.codeRaw = codeOutput.innerText;

                    codePre.querySelectorAll('code').forEach(block => {
                        Prism.highlightElement(block);
                    });
                };

            });
        </script>
    @endif
@endpush
