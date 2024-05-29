@php
    $prompt_filters = [
        'all' => 'All',
        'favorite' => 'Favorite',
    ];
@endphp

<div
    class="lqd-chat-form-wrap sticky -bottom-px z-30 rounded-b-[inherit]"
    x-data="{
        promptLibraryShow: false,
        togglePromptLibraryShow() { this.promptLibraryShow = !this.promptLibraryShow },
        promptFilter: 'all',
        changePromptFilter(filter) { filter !== this.promptFilter && (this.promptFilter = filter) },
        searchPromptStr: '',
        setSearchPromptStr(str) { this.searchPromptStr = str.trim().toLowerCase() },
        prompt: '',
        setPrompt(prompt) { this.prompt = prompt },
        focusOnPrompt() { $nextTick(() => $refs.prompt.focus()) }
    }"
>
    {{-- using form element cause issues in webchat after analyzing a website --}}
    <div
        class="lqd-chat-form flex w-full items-end gap-3 self-end rounded-ee-[inherit] bg-background/95 p-8 py-6 backdrop-blur-lg backdrop-saturate-150 max-md:items-end max-md:p-4 max-sm:p-3"
        id="chat_form"
    >
        @csrf
        <input
            id="category_id"
            type="hidden"
            value="{{ $category->id }}"
        />
        <input
            id="chat_id"
            type="hidden"
            value="{{ isset($chat) ? $chat->id : null }}"
        />
        <div class="lqd-chat-form-inputs-container flex min-h-[52px] w-full flex-col rounded-[26px] border border-input-border max-md:min-h-[45px]">
            <div
                class="hidden max-h-32 w-full grid-cols-3 gap-5 overflow-y-auto p-2.5 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 [&.active]:grid"
                id="chat_images"
            ></div>

            <div
                class="hidden max-h-32 w-full grid-cols-3 gap-5 overflow-y-auto p-2.5 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 [&.active]:grid"
                id="chat_pdfs"
            ></div>

            <hr class="split_line border-1 mb-2.5 hidden w-full" />

            <div class="relative flex grow items-center">
                <input
                    id="selectImageInput"
                    type="file"
                    style="display: none;"
                    @if ($category->slug != 'ai_vision' && $category->slug != 'ai_pdf') accept="image/*" @endif
                />

                <x-button
                    class="lqd-chat-mobile-options-trigger size-8 ms-1 mt-[3px] shrink-0 origin-center transition-transform md:hidden [&.active]:rotate-45"
                    @click.prevent="toggleMobileOptions()"
                    @click.outside="mobileOptionsShow && (mobileOptionsShow = false)"
                    ::class="{ 'active': mobileOptionsShow }"
                    size="none"
                    variant="ghost"
                    tag="button"
                >
                    <x-tabler-plus class="size-4" />
                    <span class="sr-only">{{ __('Options') }}</span>
                </x-button>

                <x-forms.input
                    id="prompt"
                    @class([
                        'm-0 w-full border-none bg-transparent py-3 pe-[100px] text-heading-foreground focus:outline-none focus:ring-0 max-md:max-h-[200px] max-md:pe-2 max-md:ps-0 max-md:text-[16px]',
                        'ps-16' => $category->slug !== 'ai_pdf',
                    ])
                    container-class="w-full"
                    type="textarea"
                    placeholder="{{ __('Type a message') }}"
                    name="prompt"
                    rows="1"
                    x-model="prompt"
                    x-ref="prompt"
                    ::bind="prompt"
                />

                <div class="pointer-events-none absolute bottom-0 end-2 start-2 flex items-end justify-between py-[5px] text-sm max-md:static">
                    <div
                        class="flex grow items-center justify-between max-md:invisible max-md:absolute max-md:-end-12 max-md:-start-1 max-md:bottom-full max-md:mb-3 max-md:translate-y-1 max-md:scale-95 max-md:flex-col max-md:items-start max-md:gap-4 max-md:rounded-xl max-md:bg-background max-md:px-4 max-md:py-0 max-md:opacity-0 max-md:shadow-lg max-md:transition-all md:flex md:h-full max-md:[&.active]:visible max-md:[&.active]:translate-y-0 max-md:[&.active]:scale-100 max-md:[&.active]:opacity-100"
                        id="chat-options"
                        :class="{ 'active': mobileOptionsShow }"
                    >
                        <div @class([
                            'pointer-events-auto max-md:pt-4',
                            'flex items-center' => $category->slug !== 'ai_pdf',
                            'hidden' => $category->slug === 'ai_pdf',
                        ])>
                            <button
                                class="lqd-chat-attach max-md:!text-heading size-10 flex shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full bg-secondary text-secondary-foreground transition-all max-md:h-auto max-md:w-auto max-md:bg-transparent max-md:text-heading-foreground md:hover:bg-secondary md:hover:opacity-80"
                                type="button"
                                @if ($app_is_demo) onclick='return toastr.info("@lang('This feature is disabled in Demo version.')")' @else id="chat_add_image" @endif
                            >
                                <x-tabler-paperclip
                                    class="size-5"
                                    stroke-width="1.5"
                                />
                                <span class="md:hidden">{{ __('Upload a document or image') }}</span>
                            </button>
                        </div>
                        @if (setting('user_prompt_library') == null || setting('user_prompt_library'))
                            <div @class([
                                'pointer-events-auto flex items-center max-md:flex-col max-md:items-start max-md:gap-4 max-md:pb-4 md:ms-auto',
                                'max-md:pt-4' => $category->slug === 'ai_pdf',
                            ])>
                                <button
                                    class="lqd-chat-templates-trigger size-10 flex shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full text-heading-foreground transition-all max-md:h-auto max-md:w-auto max-md:bg-transparent md:hover:bg-secondary md:hover:text-secondary-foreground"
                                    type="button"
                                    @click.prevent="togglePromptLibraryShow()"
                                >
                                    <x-tabler-article
                                        class="size-6"
                                        stroke-width="1.5"
                                    />
                                    <span class="md:hidden">{{ __('Browse prompt library') }}</span>
                                </button>
                            </div>
                        @endif

                    </div>
                    <div class="pointer-events-auto max-md:absolute max-md:bottom-[10px] max-md:end-2">
                        <button
                            class="lqd-chat-record-trigger size-10 flex shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full text-heading-foreground transition-all max-md:h-auto max-md:w-auto max-md:bg-transparent md:hover:bg-secondary md:hover:text-secondary-foreground [&.inactive]:hidden"
                            id="voice_record_button"
                            type="button"
                            title={{ __('Record audio') }}
                        >
                            <x-tabler-microphone
                                class="size-6"
                                stroke-width="1.5"
                            />
                        </button>
                        <button
                            class="lqd-chat-record-stop-trigger size-10 hidden shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full text-heading-foreground transition-all max-md:h-auto max-md:w-auto max-md:bg-transparent md:hover:bg-secondary md:hover:text-secondary-foreground [&.active]:flex"
                            id="voice_record_stop_button"
                            type="button"
                            title={{ __('Stop recording') }}
                        >
                            <x-tabler-player-pause-filled
                                class="size-5"
                                stroke-width="1.5"
                            />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <input
            id="chatbot_id"
            type="hidden"
            value="{{ $category->chatbot_id }}"
        />
        <input
            id="category_id"
            type="hidden"
            value="{{ $category->id }}"
        />
        <input
            id="chat_id"
            type="hidden"
            value="{{ isset($chat) ? $chat->id : null }}"
        />
        <x-button
            class="lqd-chat-send-btn size-[52px] max-md:size-10 aspect-square shrink-0 max-md:min-h-[45px] max-md:min-w-[45px]"
            id="{{ $category->slug == 'ai_vision' && $app_is_demo ? '' : 'send_message_button' }}"
            size="none"
            tag="button"
            onclick="{!! $category->slug == 'ai_vision' && $app_is_demo ? 'return toastr.info(\'{{ __('This feature is disabled in Demo version.') }}\')' : '' !!}"
            type="submit"
        >
            <x-tabler-send-2
                class="size-6 rtl:-scale-x-100"
                stroke-width="1.5"
            />
        </x-button>
        <x-button
            class="lqd-chat-stop-btn size-[52px] max-md:size-10 hidden aspect-square shrink-0 max-md:min-h-[45px] max-md:min-w-[45px] [&.active]:flex"
            id="stop_button"
            size="none"
            tag="button"
        >
            <x-tabler-hand-stop
                class="size-6"
                stroke-width="1.5"
            />
        </x-button>

    </div>

    @include('panel.user.openai_chat.components.prompt_library_modal')
</div>
