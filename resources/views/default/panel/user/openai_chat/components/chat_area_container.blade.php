@php
    $test_commands = ['Explain an Image', 'Summarize a book for research', 'Translate a book'];
    $disable_actions = $app_is_demo && (isset($category) && ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image'));
@endphp

<div
    class="conversation-area flex h-[inherit] grow flex-col justify-between overflow-y-auto rounded-b-[inherit] rounded-t-[inherit] max-md:max-h-full"
    id="chat_area_to_hide"
>

    @if (view()->hasSection('chat_head'))
        @yield('chat_head')
    @else
        @include('panel.user.openai_chat.components.chat_head')
    @endif

    <div class="relative flex grow flex-col">
        <div @class([
            'chats-container text-xs p-8 max-md:p-4 overflow-x-hidden',
            'md:mb-auto md:pb-6 relative z-10' => $category->slug == 'ai_vision',
            'h-full' => $category->slug != 'ai_vision',
        ])>

            @if (view()->hasSection('chat_area'))
                @yield('chat_area')
            @else
                @include('panel.user.openai_chat.components.chat_area')
            @endif

            @if ($category->slug == 'ai_vision' && ((isset($lastThreeMessage) && $lastThreeMessage->count() == 0) || !isset($lastThreeMessage)))
                <div
                    class="flex flex-col items-center justify-center gap-y-3"
                    id="sugg"
                >
                    <div class="flex flex-wrap items-center gap-2 text-2xs font-medium leading-relaxed text-heading-foreground">
                        {{ __('Upload an image and ask me anything') }}
                        <x-tabler-chevron-down class="size-4" />
                    </div>

                    @foreach ($test_commands as $command)
                        <x-button
                            class="font-normal"
                            tag="button"
                            variant="secondary"
                            onclick="addText('{{ __($command) }}');"
                        >
                            {{ __($command) }}
                        </x-button>
                    @endforeach
                </div>
            @endif
        </div>

        @if ($category->slug == 'ai_vision' && ((isset($lastThreeMessage) && $lastThreeMessage->count() == 0) || !isset($lastThreeMessage)))
            <div
                class="relative z-10 mt-auto flex items-center justify-center px-4 pb-5 md:px-8"
                id="mainupscale_src"
                ondrop="dropHandler(event, 'upscale_src');"
                ondragover="dragOverHandler(event);"
            >
                <label
                    class="flex w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-foreground/10 bg-background px-4 py-8 transition-colors hover:bg-foreground/10"
                    for="upscale_src"
                >
                    <div class="flex flex-col items-center justify-center">
                        <x-tabler-cloud-upload
                            class="size-11 mb-4"
                            stroke-width="1.5"
                        />

                        <span class="mb-1 block text-sm font-semibold">
                            {{ __('Drop your image here or browse') }}
                        </span>

                        <span class="file-name mb-0 block text-2xs">
                            @if ($category->slug != 'ai_vision' && $category->slug != 'ai_pdf')
                                {{ __('(Only jpg, png, webp will be accepted)') }}
                            @else
                                {{ __('(Only jpg, png and webp will be accepted)') }}
                            @endif
                        </span>
                    </div>
                    <input
                        class="hidden"
                        id="upscale_src"
                        type="file"
                        accept="@if ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf') .png, .jpg, .jpeg, .pdf @else .png, .jpg, .jpeg @endif"
                        onchange="handleFileSelect('upscale_src')"
                    />
                </label>
            </div>
        @endif
    </div>

    @if (view()->hasSection('chat_form'))
        @yield('chat_form')
    @else
        @include('panel.user.openai_chat.components.chat_form')
    @endif
</div>
