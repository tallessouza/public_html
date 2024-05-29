<x-card
    id="workbook_textarea"
    @class(['w-full [&_.tox-edit-area__iframe]:!bg-transparent'])
    variant="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.variant', 'solid') }}"
    size="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.size', 'md') }}"
    roundness="{{ Theme::getSetting('defaultVariations.card.roundness', 'default') === 'default' ? 'none' : Theme::getSetting('defaultVariations.card.roundness', 'default') }}"
>
    <div class="lqd-generator-actions flex w-full flex-wrap items-center gap-3 text-2xs">
        @include('panel.user.openai.components.workbook-actions', [
            'type' => $workbook->generator->type,
            'title' => $workbook->title,
            'slug' => $workbook->slug,
            'output' => $workbook->output,
            'is_generated_doc' => true,
        ])
    </div>

    <div class="lqd-generator-form-wrap mt-4 min-h-full w-full border-t pt-6">
        @if ($workbook->generator->type === 'code')
            <input
                id="code_lang"
                type="hidden"
                value="{{ substr($workbook->input, strrpos($workbook->input, 'in') + 3) }}"
            >
            <div
                class="line-numbers min-h-full resize [direction:ltr] [&_kbd]:inline-flex [&_kbd]:rounded [&_kbd]:bg-primary/10 [&_kbd]:px-1 [&_kbd]:py-0.5 [&_kbd]:font-semibold [&_kbd]:text-primary [&_pre[class*=language]]:my-4 [&_pre[class*=language]]:rounded"
                id="code-pre"
            ><code id="code-output">{{ $workbook->output }}</code></div>
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
                onsubmit="editWorkbook('{{ $workbook->slug }}'); return false;"
                method="POST"
            >
                <x-forms.input
                    class="border-transparent font-serif text-2xl"
                    id="workbook_title"
                    placeholder="{{ __('Untitled Document...') }}"
                    value="{{ $workbook->title }}"
                />
                <x-forms.input
                    class="tinymce border-0 font-body"
                    id="workbook_text"
                    type="textarea"
                    rows="25"
                >{!! $workbook->output !!}</x-forms.input>
                <x-button
                    class="w-full"
                    id="workbook_button"
                    tag="button"
                    type="submit"
                    variant="primary"
                    size="lg"
                >
                    <span class="group-[&.loading]/form:hidden">{{ __('Save') }}</span>
                    <span class="hidden group-[&.loading]/form:inline-block">{{ __('Please wait...') }}</span>
                </x-button>
                @csrf
            </form>
        @endif
    </div>
</x-card>
