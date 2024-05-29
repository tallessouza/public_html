@php
    $items_per_page = 5;
    $total_items = $userOpenai->total();
    $prompt_filters = [
        'all' => 'All',
        'favorite' => 'Favorite',
    ];
@endphp

<x-card
    class="lqd-image-generator mb-10 border-0 bg-secondary dark:bg-surface"
    size="lg"
    x-data="{
        promptLibraryShow: false,
        togglePromptLibraryShow() { this.promptLibraryShow = !this.promptLibraryShow },
        promptFilter: 'all',
        changePromptFilter(filter) { filter !== this.promptFilter && (this.promptFilter = filter) },
        searchPromptStr: '',
        setSearchPromptStr(str) { this.searchPromptStr = str.trim().toLowerCase() },
        setPrompt(prompt) { this.prompt = prompt },
        focusOnPrompt() { $nextTick(() => $refs.prompt.focus()) },
    
        activeGenerator: 'dall-e',
        changeActiveGenerator(tab) {
            if (tab === this.activeGenerator) return;
            if (!document.startViewTransition) {
                return this.activeGenerator = tab;
            }
            document.startViewTransition(() => this.activeGenerator = tab);
        },
        prompts: [
            '{{ addslashes(__('Cityscape at sunset in retro vector illustration')) }}',
            '{{ addslashes(__('Painting of a flower vase on a kitchen table with a window in the backdrop.')) }}',
            '{{ addslashes(__('Memphis style painting of a flower vase on a kitchen table with a window in the backdrop.')) }}',
            '{{ addslashes(__('Illustration of a cat sitting on a couch in a living room with a coffee mug in its hand.')) }}',
            '{{ addslashes(__('Delicious pizza with all the toppings.')) }}',
            '{{ addslashes(__('a super detailed infographic of a working time machine 8k')) }}',
            '{{ addslashes(__('hedgehog smelling a flower')) }}',
            '{{ addslashes(__('Freeform ferrofluids, beautiful dark chaos')) }}',
            '{{ addslashes(__('a home built in a huge Soap bubble, windows')) }}',
            '{{ addslashes(__('photo of an extremely cute alien fish swimming an alien habitable underwater planet')) }}',
        ],
        generateRandomPrompt() {
            return this.prompts[Math.floor(Math.random() * this.prompts.length)];
        },
        prompt: ''
    }"
>
    <div class="mb-5 flex flex-wrap justify-between">
        <div class="lqd-image-generator-tabs-nav flex gap-1">
            <x-button
                class="lqd-image-generator-tabs-trigger active py-2 text-2xs font-bold text-heading-foreground hover:shadow-none [&.active]:bg-foreground/10"
                data-generator-name="dall-e"
                tag="button"
                type="button"
                variant="ghost"
                x-data
                ::class="{ 'active': activeGenerator === 'dall-e' }"
                x-bind:data-active="activeGenerator === 'dall-e'"
                @click="changeActiveGenerator('dall-e')"
            >
                {{ __('DALL-E') }}
            </x-button>
            @if (setting('stable_hidden', 0) != 1)
                <x-button
                    class="lqd-image-generator-tabs-trigger py-2 text-2xs font-bold text-heading-foreground hover:shadow-none [&.active]:bg-foreground/10"
                    data-generator-name="stablediffusion"
                    tag="button"
                    type="button"
                    variant="ghost"
                    x-data
                    ::class="{ 'active': activeGenerator === 'stablediffusion' }"
                    x-bind:data-active="activeGenerator === 'stablediffusion'"
                    @click="changeActiveGenerator('stablediffusion')"
                >
                    {{ __('Stable Diffusion') }}
                </x-button>
            @endif
        </div>

        <div class="max-sm:-order-1 max-sm:mb-4 max-sm:w-full">
            <x-remaining-credit
                class="text-2xs"
                class:progressbar-image="bg-[#9E9EFF]"
                class:legend-image-box="bg-[#9E9EFF]"
                progress-height="sm"
                legend-size="sm"
                style="inline"
            />
        </div>
    </div>

    <div
        class="lqd-image-generator-tabs-content lqd-image-generator-dalle"
        x-data
        :class="{ 'hidden': activeGenerator !== 'dall-e' }"
    >
        <form
            class="lqd-image-generator-dalle-form flex flex-col items-start gap-4"
            id="openai_generator_form"
            onsubmit="return sendOpenaiGeneratorForm();"
            x-data="{ advancedSettingsShow: false }"
        >
            <h3
                class="flex w-full flex-wrap items-center gap-2"
                :class="{ 'hidden': activeGenerator === 'stablediffusion' }"
            >
                {{ __('Explain your idea') }}. |
                <button
                    class="lqd-image-generator-random-prompt-trigger cursor-pointer text-green-600 hover:underline"
                    type="button"
                    x-data
                    @click="prompt = generateRandomPrompt()"
                >
                    {{ __('Generate example prompt') }}
                </button>

                @if (setting('user_ai_image_prompt_library') == null || setting('user_ai_image_prompt_library'))
                    <button
                        class="lqd-generator-templates-trigger size-10 flex shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full text-heading-foreground transition-all max-md:h-auto max-md:w-auto max-md:bg-transparent md:hover:bg-heading-background md:hover:text-heading-foreground"
                        type="button"
                        @click.prevent="togglePromptLibraryShow()"
                    >
                        <x-tabler-article
                            class="size-6"
                            stroke-width="1.5"
                        />
                        <span class="md:hidden">{{ __('Browse prompt library') }}</span>
                    </button>
                @endif
            </h3>

            <div class="lqd-image-generator-inputs-wrap relative w-full">
                @foreach (json_decode($openai->questions) ?? [] as $question)
                    @if ($question->type == 'textarea')
                        <x-forms.input
                            class="lqd-image-generator-prompt max-md:min-h-32 h-14 resize-none overflow-hidden rounded-full bg-background px-6 py-4 text-heading-foreground shadow-sm placeholder:text-foreground/50 max-md:rounded-md"
                            id="{{ $question->name }}"
                            type="textarea"
                            name="{{ $question->name }}"
                            x-data
                            ::value="prompt"
                            ::placeholder="generateRandomPrompt()"
                        />
                    @endif
                @endforeach

                <x-button
                    class="absolute end-4 top-1/2 -translate-y-1/2 hover:-translate-y-1/2 hover:scale-110 max-lg:relative max-lg:right-auto max-lg:top-auto max-lg:mt-2 max-lg:w-full max-lg:translate-y-0"
                    id="openai_generator_button"
                    tag="button"
                    type="submit"
                >
                    {{ __('Generate') }}
                    <x-tabler-arrow-right class="size-5" />
                </x-button>
            </div>

            <x-button
                class="lqd-generator-advanced-trigger group text-3xs font-semibold text-heading-foreground"
                tag="button"
                type="button"
                variant="link"
                @click="advancedSettingsShow = !advancedSettingsShow"
            >
                {{ __('Advanced Settings') }}
                <span class="size-9 inline-flex items-center justify-center rounded-full bg-background shadow-sm">
                    <x-tabler-plus
                        class="size-4"
                        ::class="{ 'hidden': advancedSettingsShow }"
                    />
                    <x-tabler-minus
                        class="size-4 hidden"
                        ::class="{ 'hidden': !advancedSettingsShow }"
                    />
                </span>
            </x-button>

            <div
                class="hidden w-full flex-wrap justify-between gap-3"
                x-data
                x-show="advancedSettingsShow"
                :class="{ 'hidden': !advancedSettingsShow, 'flex': advancedSettingsShow }"
            >
                @include('panel.user.openai.components.generator_image_dalle_options')
            </div>
        </form>
    </div>

    <div
        class="lqd-image-generator-tabs-content lqd-image-generator-stablediffusion hidden"
        x-data="{
            activeTool: 'text-to-image',
            setActiveTool(tool) {
                if (this.activeTool === tool) return;
                if (!document.startViewTransition) {
                    return this.activeTool = tool;
                }
                document.startViewTransition(() => this.activeTool = tool);
            }
        }"
        :class="{ 'hidden': activeGenerator !== 'stablediffusion' }"
    >
        <form
            class="lqd-image-generator-stablediffusion-form flex flex-col"
            id="openai_generator_form"
            onsubmit="return sendOpenaiGeneratorForm();"
        >
            <div class="lqd-tabs-nav mb-5 flex flex-wrap">
                <x-button
                    class="lqd-tabs-trigger active grow rounded-e-none bg-background text-foreground hover:translate-y-0 hover:bg-primary hover:text-primary-foreground [&.active]:bg-primary [&.active]:text-primary-foreground"
                    variant="ghost"
                    tag="button"
                    onclick="handleTabClick('text-to-image')"
                    @click="setActiveTool('text-to-image')"
                    ::class="{ active: activeTool === 'text-to-image' }"
                >
                    {{ __('Text-to-Image') }}
                </x-button>
                <x-button
                    class="lqd-tabs-trigger grow rounded-none bg-background text-foreground hover:translate-y-0 hover:bg-primary hover:text-primary-foreground [&.active]:bg-primary [&.active]:text-primary-foreground"
                    variant="ghost"
                    tag="button"
                    onclick="handleTabClick('image-to-image')"
                    @click="setActiveTool('image-to-image')"
                    ::class="{ active: activeTool === 'image-to-image' }"
                >
                    {{ __('Image-to-Image') }}
                </x-button>
                <x-button
                    class="lqd-tabs-trigger grow rounded-none bg-background text-foreground hover:translate-y-0 hover:bg-primary hover:text-primary-foreground [&.active]:bg-primary [&.active]:text-primary-foreground"
                    variant="ghost"
                    tag="button"
                    onclick="handleTabClick('upscale')"
                    @click="setActiveTool('upscale')"
                    ::class="{ active: activeTool === 'upscale' }"
                >
                    {{ __('Upscaling') }}
                </x-button>
                <x-button
                    class="lqd-tabs-trigger grow rounded-s-none bg-background text-foreground hover:translate-y-0 hover:bg-primary hover:text-primary-foreground [&.active]:bg-primary [&.active]:text-primary-foreground"
                    variant="ghost"
                    tag="button"
                    onclick="handleTabClick('multi-prompt')"
                    @click="setActiveTool('multi-prompt')"
                    ::class="{ active: activeTool === 'multi-prompt' }"
                >
                    {{ __('Multi-Prompting') }}
                </x-button>
            </div>

            <h3
                class="lqd-image-generator-random-prompt m-0 flex flex-wrap items-center gap-2"
                x-show="activeTool !== 'upscale'"
                x-collapse
            >
                {{ __('Explain your idea') }}. |
                <button
                    class="lqd-image-generator-random-prompt-trigger cursor-pointer text-green-600 hover:underline"
                    type="button"
                    x-data
                    @click="prompt = generateRandomPrompt()"
                >
                    {{ __('Generate example prompt') }}
                </button>
                @if (setting('user_ai_image_prompt_library') == null || setting('user_ai_image_prompt_library'))
                    <button
                        class="lqd-generator-templates-trigger size-10 flex shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full text-heading-foreground transition-all max-md:h-auto max-md:w-auto max-md:bg-transparent md:hover:bg-heading-background md:hover:text-heading-foreground"
                        type="button"
                        @click.prevent="togglePromptLibraryShow()"
                    >
                        <x-tabler-article
                            class="size-6"
                            stroke-width="1.5"
                        />
                        <span class="md:hidden">{{ __('Browse prompt library') }}</span>
                    </button>
                @endif
            </h3>

            <div
                class="lqd-tabs-content mt-5 flex-col items-start"
                :class="{ 'hidden': activeTool !== 'text-to-image', 'flex': activeTool === 'text-to-image' }"
            >
                <div class="lqd-image-generator-inputs-wrap relative w-full">
                    @foreach (json_decode($openai->questions) ?? [] as $question)
                        @if ($question->type == 'textarea')
                            <x-forms.input
                                class="max-md:min-h-32 h-14 resize-none overflow-hidden rounded-full bg-background px-6 py-4 text-heading-foreground shadow-sm placeholder:text-foreground/50 max-md:rounded-md"
                                id="txt2img_description"
                                name="txt2img_description"
                                container-class="w-full"
                                type="textarea"
                                x-data
                                ::value="prompt"
                                ::placeholder="generateRandomPrompt()"
                            />
                        @endif
                    @endforeach

                    <x-button
                        class="absolute end-4 top-1/2 -translate-y-1/2 hover:-translate-y-1/2 hover:scale-110 max-lg:relative max-lg:right-auto max-lg:top-auto max-lg:mt-2 max-lg:w-full max-lg:translate-y-0"
                        id="openai_generator_button"
                        tag="button"
                        type="submit"
                    >
                        {{ __('Generate') }}
                        <x-tabler-arrow-right class="size-5" />
                    </x-button>
                </div>
            </div>

            <div
                class="lqd-tabs-content mt-5 hidden flex-col items-start gap-4"
                :class="{ 'hidden': activeTool !== 'image-to-image', 'flex': activeTool === 'image-to-image' }"
            >
                <x-forms.input
                    class="max-md:min-h-32 h-14 resize-none overflow-hidden rounded-full bg-background px-6 py-4 text-heading-foreground shadow-sm placeholder:text-foreground/50 max-md:rounded-md"
                    id="img2img_description"
                    container-class="w-full"
                    name="img2img_description"
                    type="textarea"
                    x-data
                    ::value="prompt"
                    ::placeholder="generateRandomPrompt()"
                />

                <label
                    class="w-full cursor-pointer text-lg font-semibold text-heading-foreground"
                    for="img2img_src"
                >{{ __('Upload Image') }}</label>

                <div
                    class="flex w-full items-center justify-center"
                    ondrop="dropHandler(event, 'img2img_src');"
                    ondragover="dragOverHandler(event);"
                >
                    <label
                        class="lqd-filepicker-label min-h-64 flex w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-foreground/10 bg-background text-center transition-colors hover:bg-background/80"
                        for="img2img_src"
                    >
                        <div class="flex flex-col items-center justify-center py-6">
                            <x-tabler-cloud-upload
                                class="size-11 mb-4"
                                stroke-width="1.5"
                            />

                            <p class="mb-1 text-sm font-semibold">
                                {{ __('Drop your image here or browse') }}
                            </p>

                            <p class="file-name mb-0 text-2xs">
                                {{ __('(Only jpg, png, webp will be accepted)') }}
                            </p>
                        </div>

                        <input
                            class="hidden"
                            id="img2img_src"
                            type="file"
                            accept=".png, .jpg, .jpeg"
                            onchange="handleFileSelect('img2img_src')"
                        />
                    </label>
                </div>

                <x-button
                    class="w-full"
                    id="openai_generator_button"
                    tag="button"
                    type="submit"
                    size="lg"
                >
                    {{ __('Generate') }}
                    <x-tabler-arrow-right class="size-5" />
                </x-button>
            </div>

            <div
                class="lqd-tabs-content hidden flex-col items-start"
                :class="{ 'hidden': activeTool !== 'upscale', 'flex': activeTool === 'upscale' }"
            >
                <label
                    class="mb-5 w-full cursor-pointer text-lg font-semibold text-heading-foreground"
                    for="upscale_src"
                >{{ __('Upload Image') }}</label>

                <div
                    class="mb-4 flex w-full items-center justify-center"
                    ondrop="dropHandler(event, 'upscale_src');"
                    ondragover="dragOverHandler(event);"
                >
                    <label
                        class="lqd-filepicker-label min-h-64 flex w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-foreground/10 bg-background text-center transition-colors hover:bg-background/80"
                        for="upscale_src"
                    >
                        <div class="flex flex-col items-center justify-center py-6">
                            <x-tabler-cloud-upload
                                class="size-11 mb-4"
                                stroke-width="1.5"
                            />

                            <p class="mb-1 text-sm font-semibold">
                                {{ __('Drop your image here or browse') }}
                            </p>

                            <p class="file-name mb-0 text-2xs">
                                {{ __('(Only jpg, png, webp will be accepted)') }}
                            </p>
                        </div>

                        <input
                            class="hidden"
                            id="upscale_src"
                            type="file"
                            accept=".png, .jpg, .jpeg"
                            onchange="handleFileSelect('upscale_src')"
                        />
                    </label>
                </div>

                <x-button
                    class="w-full"
                    id="openai_generator_button"
                    tag="button"
                    type="submit"
                    size="lg"
                >
                    {{ __('Generate') }}
                    <x-tabler-arrow-right class="size-5" />
                </x-button>
            </div>

            <div
                class="lqd-tabs-content mt-5 hidden flex-col items-start gap-4"
                :class="{ 'hidden': activeTool !== 'multi-prompt', 'flex': activeTool === 'multi-prompt' }"
            >
                <x-forms.input
                    class="multi_prompts_description max-md:min-h-32 h-14 resize-none overflow-hidden rounded-full bg-background px-6 py-4 text-heading-foreground shadow-sm placeholder:text-foreground/50 max-md:rounded-md"
                    id="multi_prompts_description"
                    name="multi_prompts_description"
                    container-class="w-full"
                    type="textarea"
                    x-data
                    ::value="prompt"
                    ::placeholder="generateRandomPrompt()"
                />

                <div class="multi-prompts flex w-full flex-col gap-2 empty:hidden"></div>

                <x-button
                    class="bg-background text-heading-foreground hover:bg-emerald-500 hover:text-white dark:bg-foreground/10 dark:hover:bg-emerald-500"
                    tag="button"
                    variant="ghost"
                    onclick="return handleAddPrompt();"
                >
                    <x-tabler-plus class="size-4" />
                    {{ __('Add More') }}
                </x-button>

                <x-button
                    class="mt-2 w-full"
                    id="openai_generator_button"
                    tag="button"
                    type="submit"
                    size="lg"
                >
                    {{ __('Generate') }}
                    <x-tabler-arrow-right class="size-5" />
                </x-button>
            </div>

            <div
                class="mt-5 flex flex-col items-start"
                x-data="{ advancedSettingsShow: false }"
            >
                <x-button
                    class="lqd-generator-advanced-trigger group text-3xs font-semibold text-heading-foreground"
                    tag="button"
                    type="button"
                    variant="link"
                    @click="advancedSettingsShow = !advancedSettingsShow"
                >
                    {{ __('Advanced Settings') }}
                    <span class="size-9 inline-flex items-center justify-center rounded-full bg-background shadow-sm">
                        <x-tabler-plus
                            class="size-4"
                            ::class="{ 'hidden': advancedSettingsShow }"
                        />
                        <x-tabler-minus
                            class="size-4 hidden"
                            ::class="{ 'hidden': !advancedSettingsShow }"
                        />
                    </span>
                </x-button>

                <div
                    class="hidden w-full flex-col justify-between gap-4"
                    x-data
                    x-show="advancedSettingsShow"
                    :class="{ 'hidden': !advancedSettingsShow, 'flex': advancedSettingsShow }"
                >
                    @include('panel.user.openai.components.generator_image_stablediffusion_options')
                </div>
            </div>
        </form>
    </div>

    @include('panel.user.openai_chat.components.prompt_library_modal')
</x-card>

<div
    id="generator_sidebar_table"
    x-data="{
        modalShow: false,
        activeItem: null,
        activeItemId: null,
        setActiveItem(data) {
            this.activeItem = data;
            this.activeItemId = data.id;
        },
        prevItem() {
            const currentEl = document.querySelector(`.image-result[data-id='${this.activeItemId}']`);
            const prevEl = currentEl?.previousElementSibling;
            if (!prevEl) return;
            const data = JSON.parse(prevEl.querySelector('.lqd-image-result-view').getAttribute('data-payload') || {});
            this.setActiveItem(data);
        },
        nextItem() {
            const currentEl = document.querySelector(`.image-result[data-id='${this.activeItemId}']`);
            const nextEl = currentEl?.nextElementSibling;
            if (!nextEl) return;
            const data = JSON.parse(nextEl.querySelector('.lqd-image-result-view').getAttribute('data-payload') || {});
            this.setActiveItem(data);
        },
    }"
    @keyup.escape.window="modalShow = false"
>
    <h2 class="mb-5">{{ __('Result') }}</h2>
    <div class="image-results grid grid-cols-2 gap-8 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
        @foreach ($userOpenai->take($items_per_page) as $item)
            <div
                class="image-result lqd-loading-skeleton group w-full"
                data-id="{{ $item->id }}"
                data-generator="{{ str()->lower($item->response) }}"
            >
                <figure
                    class="lqd-image-result-fig relative mb-3 aspect-square overflow-hidden rounded-lg shadow-md transition-all group-hover:-translate-y-1 group-hover:scale-105 group-hover:shadow-lg"
                    data-lqd-skeleton-el
                >
                    <img
                        class="lqd-image-result-img aspect-square h-full w-full object-cover object-center"
                        loading="lazy"
                        src="{{ ThumbImage($item->output) }}"
                    >
                    <div
                        class="lqd-image-result-actions absolute inset-0 flex w-full flex-col items-center justify-center gap-2 p-4 transition-opacity group-[&.lqd-is-loading]:invisible group-[&.lqd-is-loading]:opacity-0">
                        <div class="opacity-0 transition-opacity group-hover:opacity-100">
                            <x-button
                                class="lqd-image-result-download download size-9 rounded-full bg-background text-foreground hover:bg-background hover:bg-emerald-400 hover:text-white"
                                size="none"
                                href="{{ $item->output }}"
                                download="{{ $item->slug }}"
                            >
                                <x-tabler-download class="size-5" />
                            </x-button>
                            <x-button
                                class="lqd-image-result-view gallery size-9 rounded-full bg-background text-foreground hover:bg-background hover:bg-emerald-400 hover:text-white"
                                data-payload="{{ $item }}"
                                @click.prevent="setActiveItem( JSON.parse($el.getAttribute('data-payload') || {}) ); modalShow = true"
                                size="none"
                                href="#"
                            >
                                <x-tabler-eye class="size-5" />
                            </x-button>
                            <x-button
                                class="lqd-image-result-delete delete size-9 rounded-full bg-background text-foreground hover:bg-background hover:bg-red-500 hover:text-white"
                                size="none"
                                onclick="return confirm('Are you sure?')"
                                href="{{ route('dashboard.user.openai.documents.image.delete', $item->slug) }}"
                            >
                                <x-tabler-x class="size-4" />
                            </x-button>
                        </div>
                        <span
                            class="lqd-image-result-type absolute bottom-4 end-4 mb-0 rounded-full bg-background px-2 py-1 text-3xs font-semibold uppercase leading-none transition-opacity group-[&.lqd-is-loading]:invisible group-[&[data-generator=de]]:text-red-500 group-[&[data-generator=sd]]:text-blue-500 group-[&.lqd-is-loading]:opacity-0"
                        >
                            {{ $item->response }}
                        </span>
                    </div>
                </figure>
                <p class="lqd-image-result-title mb-1 w-full overflow-hidden overflow-ellipsis whitespace-nowrap text-heading-foreground transition-opacity">
                    {{ $item->input }}
                </p>
            </div>
        @endforeach
    </div>

    @if ($userOpenai->count() > 0)
        <div
            class="lqd-load-more-trigger min-h-px group w-full py-8 text-center font-medium text-heading-foreground"
            data-all-loaded="false"
        >
            <span class="lqd-load-more-trigger-loading flex items-center justify-center gap-2 text-center leading-tight group-[&[data-all-loaded=true]]:hidden">
                {{ __('Loading more') }}
                <span class="flex gap-1">
                    @foreach ([0, 1, 2] as $item)
                        <span
                            class="inline-block h-[3px] w-[3px] animate-bounce-load-more rounded-full bg-current"
                            style="animation-delay: {{ $loop->index / 14 }}s"
                        ></span>
                    @endforeach
                </span>
            </span>
            <span class="lqd-load-more-trigger-all-loaded hidden items-center justify-center gap-2 text-center group-[&[data-all-loaded=true]]:flex">
                {{ __('All images loaded') }}
                <x-tabler-check class="size-5" />
            </span>
        </div>
    @endif

    {{-- Image modal --}}
    <div
        class="lqd-modal-img group/modal invisible fixed start-0 top-0 z-[999] flex h-screen w-screen flex-col items-center border p-3 opacity-0 [&.is-active]:visible [&.is-active]:opacity-100"
        id="modal_image"
        x-data
        :class="{ 'is-active': modalShow }"
    >
        <div
            class="lqd-modal-img-backdrop absolute start-0 top-0 z-0 h-screen w-screen bg-black/10 opacity-0 backdrop-blur-sm transition-opacity group-[&.is-active]/modal:opacity-100"
            @click="modalShow = false"
        ></div>

        <div class="lqd-modal-img-content-wrap relative z-10 my-auto max-h-[90vh] w-full">
            <div class="container relative h-full max-w-6xl">
                <div
                    class="lqd-modal-img-content relative flex h-full translate-y-2 scale-[0.985] flex-wrap justify-between overflow-y-auto rounded-xl bg-background p-5 opacity-0 shadow-2xl transition-all group-[&.is-active]/modal:translate-y-0 group-[&.is-active]/modal:scale-100 group-[&.is-active]/modal:opacity-100 xl:min-h-[570px]">
                    <a
                        class="size-9 absolute end-2 top-3 z-10 flex items-center justify-center rounded-full border bg-background text-inherit shadow-sm transition-all hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black"
                        @click.prevent="modalShow = false"
                        href="#"
                    >
                        <x-tabler-x class="size-4" />
                    </a>

                    <figure class="lqd-modal-fig relative aspect-square min-h-[1px] w-full rounded-lg bg-cover bg-center max-md:min-h-[350px] md:w-6/12">
                        <img
                            class="lqd-modal-img mx-auto h-full w-auto"
                            :src="activeItem?.output"
                            :alt="activeItem?.input"
                        />
                        <a
                            class="size-9 absolute bottom-7 end-7 inline-flex items-center justify-center rounded-full bg-background text-inherit shadow-sm transition-all hover:scale-105"
                            href="#"
                            :href="activeItem?.output"
                            download
                        >
                            <x-tabler-download class="size-4" />
                        </a>
                    </figure>

                    <div class="relative flex w-full flex-col p-3 md:w-5/12">
                        <div class="relative flex flex-col items-start pb-6">
                            <h3 class="mb-4">
                                {{ __('Image Details') }}
                            </h3>

                            <span
                                class="mb-3 inline-flex cursor-copy items-center justify-center gap-2 rounded-md bg-secondary px-2 py-1 text-center text-[11px] font-semibold text-secondary-foreground"
                                @click="navigator.clipboard.writeText(activeItem?.input); toastr.success('{{ __('Copied prompt') }}');"
                            >
                                <x-tabler-copy class="size-4" />
                                {{ __('Prompt') }}
                            </span>

                            <span
                                class="mt-2"
                                x-text="activeItem?.input"
                            ></span>
                        </div>

                        <div class="mt-auto flex flex-wrap justify-between gap-y-3 text-[13px] font-medium">
                            <div class="w-full md:w-[30%]">
                                <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                    <p class="mb-1">@lang('Date')</p>
                                    <p
                                        class="mb-0 opacity-60"
                                        x-text="activeItem?.format_date ?? '{{ __('None') }}'"
                                    ></p>
                                </div>
                            </div>
                            <div class="w-full md:w-[30%]">
                                <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                    <p class="mb-1">@lang('Resolution')</p>
                                    <p
                                        class="mb-0 opacity-60"
                                        x-text="activeItem?.size ?? '{{ __('None') }}'"
                                    >
                                    </p>
                                </div>
                            </div>
                            <div class="w-full md:w-[30%]">
                                <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                    <p class="mb-1">@lang('Credit')</p>
                                    <p
                                        class="mb-0 opacity-60"
                                        x-text="activeItem?.credits ?? '{{ __('None') }}'"
                                    ></p>
                                </div>
                            </div>
                            <div class="w-full md:w-[30%]">
                                <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                    <p class="mb-1">@lang('AI Model')</p>
                                    <p
                                        class="mb-0 opacity-60"
                                        x-text="activeItem?.response ?? '{{ __('None') }}'"
                                    ></p>
                                </div>
                            </div>
                            <div class="w-full md:w-[30%]">
                                <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                    <p class="mb-1">@lang('Art Style')</p>
                                    <p
                                        class="mb-0 opacity-60"
                                        x-text="activeItem?.image_style ?? '{{ __('None') }}'"
                                    ></p>
                                </div>
                            </div>
                            <div class="w-full md:w-[30%]">
                                <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                    <p class="mb-1">@lang('Mood')</p>
                                    <p
                                        class="mb-0 opacity-60"
                                        x-text="activeItem?.image_mood ?? '{{ __('None') }}'"
                                    ></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prev/Next buttons -->
                <a
                    class="size-9 absolute -start-1 top-1/2 z-10 inline-flex -translate-y-1/2 items-center justify-center rounded-full bg-background text-inherit shadow-md transition-all hover:scale-110 hover:bg-[--tblr-primary] hover:text-white"
                    href="#"
                    @click.prevent="prevItem()"
                >
                    <x-tabler-chevron-left class="size-5" />
                </a>
                <a
                    class="size-9 absolute -end-1 top-1/2 z-10 inline-flex -translate-y-1/2 items-center justify-center rounded-full bg-background text-inherit shadow-md transition-all hover:scale-110 hover:bg-[--tblr-primary] hover:text-white"
                    href="#"
                    @click.prevent="nextItem()"
                >
                    <x-tabler-chevron-right class="size-5" />
                </a>
            </div>
        </div>
    </div>
</div>

<template id="image_result">
    <div class="image-result lqd-loading-skeleton lqd-is-loading group w-full">
        <figure
            class="lqd-image-result-fig relative mb-3 aspect-square overflow-hidden rounded-lg shadow-md transition-all group-hover:-translate-y-1 group-hover:scale-105 group-hover:shadow-lg"
            data-lqd-skeleton-el
        >
            <img
                class="lqd-image-result-img aspect-square h-full w-full object-cover object-center"
                loading="lazy"
                src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgc3R5bGU9ImZpbGw6I2VlZWVlZTsiLz48L3N2Zz4="
            >
            <div
                class="lqd-image-result-actions absolute inset-0 flex w-full flex-col items-center justify-center gap-2 p-4 transition-opacity group-[&.lqd-is-loading]:invisible group-[&.lqd-is-loading]:opacity-0">
                <div class="opacity-0 transition-opacity group-hover:opacity-100">
                    <x-button
                        class="lqd-image-result-download download size-9 rounded-full bg-background text-foreground hover:bg-background hover:bg-emerald-400 hover:text-white"
                        size="none"
                        href="#"
                        download=true
                    >
                        <x-tabler-download class="size-5" />
                    </x-button>
                    <x-button
                        class="lqd-image-result-view gallery size-9 rounded-full bg-background text-foreground hover:bg-background hover:bg-emerald-400 hover:text-white"
                        @click.prevent="setActiveItem( JSON.parse($el.getAttribute('data-payload') || {}) ); modalShow = true"
                        size="none"
                        href="#"
                    >
                        <x-tabler-eye class="size-5" />
                    </x-button>
                    <x-button
                        class="lqd-image-result-delete delete size-9 rounded-full bg-background text-foreground hover:bg-background hover:bg-red-500 hover:text-white"
                        size="none"
                        onclick="return confirm('Are you sure?')"
                        href="#"
                    >
                        <x-tabler-x class="size-4" />
                    </x-button>
                </div>
                <span
                    class="lqd-image-result-type absolute bottom-4 end-4 mb-0 rounded-full bg-background px-2 py-1 text-3xs font-semibold uppercase leading-none transition-opacity group-[&.lqd-is-loading]:invisible group-[&[data-generator=de]]:text-red-500 group-[&[data-generator=sd]]:text-blue-500 group-[&.lqd-is-loading]:opacity-0"
                ></span>
            </div>
        </figure>
        <p
            class="lqd-image-result-title mb-1 w-full overflow-hidden overflow-ellipsis whitespace-nowrap text-heading-foreground transition-opacity"
            data-lqd-skeleton-el
        ></p>
    </div>
</template>

<template id="prompt-template">
    <div class="each-prompt flex items-center justify-between gap-2">
        <x-forms.input
            class="multi_prompts_description rounded-full bg-background shadow-sm placeholder:text-foreground/50"
            container-class="grow"
            size="xl"
            type="text"
            name="titles[]"
            placeholder="Type another title or description"
            required
        />

        <x-button
            class="size-11"
            data-toggle="remove-parent"
            tag="button"
            size="none"
            variant="danger"
            title="{{ __('Remove item') }}"
        >
            <x-tabler-trash class="size-5" />
        </x-button>
    </div>
</template>

@push('script')
    <script src="{{ custom_theme_url('/assets/libs/fslightbox/fslightbox.js') }}"></script>

    <script>
        var resizedImage;

        function handleTabClick(type) {
            stablediffusionType = type;
            let imageResolution = document.getElementById("image_resolution");
            let negativePrompt = document.getElementById("negative_prompt");
            let clipGuidancePreset = document.getElementById("clip_guidance_preset");
            imageResolution.disabled = false;
            negativePrompt.disabled = false;
            clipGuidancePreset.disabled = false;
            switch (type) {
                case 'text-to-image':
                    break;
                case 'image-to-image':
                    clipGuidancePreset.value = "";
                    clipGuidancePreset.disabled = true;
                    break;
                case 'upscale':
                    imageResolution.disabled = true;
                    clipGuidancePreset.value = "";
                    clipGuidancePreset.disabled = true
                    break;
                case 'multi-prompt':
                    negativePrompt.disabled = true;
                    break;
            }
        }

        function handleAddPrompt() {
            const mulPromptsContainer = document.querySelector('.multi-prompts')
            const promptTemplate = document.querySelector('#prompt-template').content.cloneNode(true)
            const removeBtn = promptTemplate.querySelector('[data-toggle="remove-parent"]')
            removeBtn.addEventListener('click', (e) => {
                event.preventDefault();
                e.currentTarget.parentElement.remove();
            })
            mulPromptsContainer.append(promptTemplate)
        }

        function dropHandler(ev, id) {
            // Prevent default behavior (Prevent file from being opened)
            ev.preventDefault();
            $('#' + id)[0].files = ev.dataTransfer.files;
            $('#' + id).prev().find(".file-name").text(ev.dataTransfer.files[0].name);
        }

        function dragOverHandler(ev) {
            // Prevent default behavior (Prevent file from being opened)
            ev.preventDefault();
        }

        function handleFileSelect(id) {
            $('#' + id).prev().find(".file-name").text($('#' + id)[0].files[0].name);
        }

        function resizeImage(e) {
            var file;
            if (stablediffusionType == 'image-to-image') {
                file = $("#img2img_src")[0].files[0];
            } else if (stablediffusionType == 'upscale') {
                file = $("#upscale_src")[0].files[0];
            }
            if (file == undefined) return;
            var reader = new FileReader();
            reader.onload = function(event) {
                var img = new Image();
                img.onload = function() {
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext("2d");
                    const img_size = $("#image_resolution").val();
                    let w = Number(img_size.split("x")[0]);
                    let h = Number(img_size.split("x")[1]);
                    if (stablediffusionType == 'upscale') {
                        if (this.width % 64 != 0) {
                            w = Math.floor(this.width / 64) * 64 + 64;
                        } else {
                            w = this.width;
                        }
                        if (this.height % 64 != 0) {
                            h = Math.floor(this.height / 64) * 64 + 64;
                        } else {
                            h = this.height;
                        }
                        if (w * h >= 1024 * 1024) {
                            let s = Math.min(w, h);
                            let b = Math.max(w, h);
                            let a = b / s;
                            let x = Math.sqrt(1024 * 1024 / a);
                            if (s == w) {
                                w = Math.floor(x / 64) * 64;
                                h = Math.floor(x * a / 64) * 64;
                            }
                        }
                    }
                    canvas.width = w;
                    canvas.height = h;
                    var ctx = canvas.getContext("2d");
                    ctx.drawImage(img, 0, 0, w, h);
                    var dataurl = canvas.toDataURL("image/png");
                    var byteString = atob(dataurl.split(',')[1]);
                    var mimeString = dataurl.split(',')[0].split(':')[1].split(';')[0];
                    var ab = new ArrayBuffer(byteString.length);
                    var ia = new Uint8Array(ab);
                    for (var i = 0; i < byteString.length; i++) {
                        ia[i] = byteString.charCodeAt(i);
                    }
                    var blob = new Blob([ab], {
                        type: mimeString
                    });
                    resizedImage = new File([blob], file.name);
                }
                img.src = event.target.result;
            }
            reader.readAsDataURL(file);
        }
        document.getElementById("img2img_src").addEventListener('change', resizeImage);
        document.getElementById("upscale_src").addEventListener('change', resizeImage);
        document.getElementById("image_resolution").addEventListener('change', resizeImage);
        // document.getElementById("image_model").addEventListener('change', dallEModelChange);

        (() => {
            "use strict";

            const itemsPerPage = {{ $items_per_page }};
            let offset = itemsPerPage; // Declare offset globally
            let totalItems = {{ $total_items }};
            let nextCount = Math.min(totalItems - itemsPerPage, itemsPerPage);
            let loadingQueue = [];

            const imageContainer = document.querySelector('.image-results');
            const loadMoreTrigger = document.querySelector('.lqd-load-more-trigger');
            const imageResultTemplate = document.querySelector('#image_result');
            const loadMoreObserver = new IntersectionObserver(([entry], observer) => {
                if (entry.isIntersecting) {
                    if (loadMoreTrigger.classList.contains('lqd-is-loading')) return;
                    createSkeleton(imageResultTemplate, nextCount);
                    lazyLoadImages()
                }
            }, {
                threshold: [1]
            });

            loadMoreObserver.observe(loadMoreTrigger);

            function createSkeleton(template, limit = 5) {
                const skeletonTemplates = [];
                for (let i = 0; i < limit; i++) {
                    const skeleton = template.content.cloneNode(true);
                    skeletonTemplates.push(skeleton);
                    imageContainer.append(skeleton);
                    loadingQueue.push(imageContainer.lastElementChild);
                }

                return skeletonTemplates;
            }

            function lazyLoadImages() {
                loadMoreTrigger.classList.add('lqd-is-loading');

                fetch(`{{ route('dashboard.user.openai.lazyloadimage') }}?offset=${offset}&post_type={{ $openai->slug }}`)
                    .then(response => response.json())
                    .then(data => {
                        const images = data.images;
                        const currenturl = window.location.href;
                        const server = currenturl.split('/')[0];

                        nextCount = Math.min(data.count_remaining, itemsPerPage);

                        images.forEach((image, index) => {
                            const imageResultTemplate = loadingQueue[index];
                            const delete_url = `${server}/dashboard/user/openai/documents/delete/image/${image.slug}`;

                            imageResultTemplate.setAttribute('data-id', image.id);
                            imageResultTemplate.setAttribute('data-generator', image.response == "SD" ? "sd" : "de");
                            imageResultTemplate.querySelector('.lqd-image-result-img').setAttribute('src', image.thumbnail);
                            imageResultTemplate.querySelector('.lqd-image-result-type').innerHTML = image.response == "SD" ? "SD" : "DE";
                            imageResultTemplate.querySelector('.lqd-image-result-view').setAttribute('data-payload', JSON.stringify(image));

                            imageResultTemplate.querySelector('.lqd-image-result-delete').setAttribute('href', delete_url);
                            imageResultTemplate.querySelector('.lqd-image-result-download').setAttribute('href', image.output);
                            imageResultTemplate.querySelector('.lqd-image-result-download').setAttribute('download', image.slug);
                            imageResultTemplate.querySelector('.lqd-image-result-title').setAttribute('title', image.input);
                            imageResultTemplate.querySelector('.lqd-image-result-title').innerText = image.input;

                            imageResultTemplate.classList.remove('lqd-is-loading');
                        });

                        loadingQueue = [];

                        // Update the offset for the next lazy loading request
                        offset += images.length;
                        // Refresh lightbox, check if there are more images
                        refreshFsLightbox();

                        loadMoreTrigger.classList.remove('lqd-is-loading');

                        if (data.count_remaining <= 0) {
                            loadMoreTrigger.setAttribute('data-all-loaded', 'true');
                            loadMoreObserver.disconnect();
                        } else {
                            // check if loadMoreTrigger is in view. if it is load more images
                            if (loadMoreTrigger.getBoundingClientRect().top <= window.innerHeight) {
                                createSkeleton(imageResultTemplate, nextCount);
                                lazyLoadImages();
                            }
                        }

                    });
            }
        })();
    </script>
@endpush
