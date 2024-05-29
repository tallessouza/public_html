<div
    class="lqd-chat-head min-h-20 sticky -top-px z-30 flex items-center justify-between gap-2 rounded-se-[inherit] border-b bg-background/80 px-5 py-3 backdrop-blur-lg backdrop-saturate-150 max-md:bg-background/95 max-md:px-4">
    <div class="flex shrink-0 gap-2">
        <div
            class="text-foreground/65 size-11 inline-flex items-center justify-center overflow-hidden overflow-ellipsis whitespace-nowrap rounded-full text-2xs font-medium"
            style="background: {{ $category->color }};"
        >
            @if ($category->slug === 'ai-chat-bot')
                <img
                    class="lqd-chat-avatar-img h-full w-full object-cover object-center"
                    src="{{ custom_theme_url('/assets/img/chat-default.jpg') }}"
                    alt="{{ __($category->name) }}"
                >
            @elseif ($category->image)
                <img
                    class="lqd-chat-avatar-img h-full w-full object-cover object-center"
                    src="{{ custom_theme_url($category->image, true) }}"
                    alt="{{ __($category->name) }}"
                >
            @else
                <span class="block w-full overflow-hidden overflow-ellipsis whitespace-nowrap text-center">
                    {{ __($category->short_name) }}
                </span>
            @endif
        </div>
        <div class="flex flex-col items-start justify-center text-sm max-sm:hidden">
            @if ($category->human_name != '')
                <p class="m-0 font-semibold text-heading-foreground">
                    {{ __($category->human_name) }}
                </p>
            @endif
            @if ($category->role != '')
                <p class="m-0 text-2xs text-heading-foreground/60">
                    {{ __($category->role) }}
                </p>
            @endif
        </div>
    </div>

    <div class="flex grow items-center justify-end gap-4">
        <div class="flex gap-2">
            @if (view()->hasSection('chat_head_actions'))
                @yield('chat_head_actions')
            @else
                <x-forms.input
                    class="max-md:hidden"
                    id="realtime"
                    container-class="{{ $category->slug == 'ai_pdf' ? 'hidden' : '' }} max-md:size-8 max-md:inline-flex max-md:items-center max-md:justify-center max-md:overflow-hidden max-md:shadow-md max-md:rounded-full max-md:shrink-0 max-md:[&_.lqd-input-label-txt]:hidden"
                    label="{{ __('Use Real-Time Data') }}"
                    type="checkbox"
                    name="realtime"
                    onchange="const checked = document.querySelector('#realtime').checked; if ( checked ) { toastr.success('Real-Time data activated') } else { toastr.warning('Real-Time data deactivated') }"
                    switcher
                >
                    <span
                        class="size-8 inline-flex shrink-0 items-center justify-center rounded-full bg-background indent-0 text-heading-foreground transition-colors peer-checked:bg-primary peer-checked:text-primary-foreground md:hidden"
                    >
                        <x-tabler-world-download
                            class="size-5"
                            stroke-width="1.5"
                        />
                    </span>
                </x-forms.input>
            @endif

            <div
                class="group relative inline-flex flex-row items-center justify-center self-center max-md:-order-1"
                id="show_export_btns"
            >
                <button class="max-md:size-8 max-md:inline-flex max-md:items-center max-md:justify-center max-md:rounded-full max-md:shadow-md">
                    <x-tabler-clipboard-copy
                        class="size-6"
                        stroke-width="1.5"
                    />
                </button>
                <div
                    class="invisible absolute -end-4 bottom-full flex translate-y-2 scale-95 flex-row items-center justify-center rounded-lg bg-primary text-primary-foreground opacity-0 transition-all group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:scale-100 group-focus-within:opacity-100 group-hover:visible group-hover:translate-y-0 group-hover:scale-100 group-hover:opacity-100"
                    id="export_btns"
                >
                    <button
                        class="chat-download border-none px-3 py-1 text-3xs font-medium"
                        id="export_pdf"
                        data-doc-type="pdf"
                    >
                        {{ __('PDF') }}
                    </button>
                    <button
                        class="chat-download border-x border-x-primary-foreground/20 px-2.5 py-1 text-3xs font-medium"
                        id="export_word"
                        data-doc-type="doc"
                    >
                        {{ __('Word') }}
                    </button>
                    <button
                        class="chat-download px-3 py-1 text-3xs font-medium"
                        id="export_txt"
                        data-doc-type="txt"
                    >
                        {{ __('Txt') }}
                    </button>
                </div>
            </div>

            @if (view()->hasSection('chat_sidebar_actions'))
                @yield('chat_sidebar_actions')
            @else
                @if (isset($category) && $category->slug == 'ai_pdf')
                    {{-- #selectDocInput is present in chat_sidebar component. no need to duplicate it here --}}
                    <x-button
                        class="lqd-upload-doc-trigger size-8 group shrink-0 grid-flow-row place-items-center rounded-full shadow-md max-md:grid md:hidden"
                        variant="none"
                        size="none"
                        href="javascript:void(0);"
                        onclick="return $('#selectDocInput').click();"
                    >
                        <x-tabler-plus class="size-5" />
                        <span class="sr-only">
                            {{ __('Upload Document') }}
                        </span>
                    </x-button>
                @else
                    <x-button
                        class="lqd-new-chat-trigger size-8 group shrink-0 grid-flow-row place-items-center rounded-full shadow-md max-md:grid md:hidden"
                        variant="none"
                        size="none"
                        href="javascript:void(0);"
                        onclick="{!! $disable_actions
                            ? 'return toastr.info(\'{{ __('This feature is disabled in Demo version.') }}\')'
                            : 'return startNewChat(\'{{ $category->id }}\', \'{{ LaravelLocalization::getCurrentLocale() }}\')' !!}"
                    >
                        <x-tabler-plus class="size-5" />
                        <span class="sr-only">
                            {{ __('New Conversation') }}
                        </span>
                    </x-button>
                @endif

                <div class="lqd-chat-mobile-sidebar-trigger self-center">
                    <button
                        class="size-8 group shrink-0 grid-flow-row place-items-center rounded-full shadow-md max-md:grid md:hidden"
                        :class="{ 'active': mobileSidebarShow }"
                        @click.prevent="toggleMobileSidebar()"
                        type="button"
                    >
                        <x-tabler-dots class="size-5 col-start-1 row-start-1 transition-all group-[&.active]:rotate-45 group-[&.active]:scale-75 group-[&.active]:opacity-0" />
                        <x-tabler-x class="size-4 col-start-1 row-start-1 -rotate-45 opacity-0 transition-all group-[&.active]:rotate-0 group-[&.active]:!opacity-100" />
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
