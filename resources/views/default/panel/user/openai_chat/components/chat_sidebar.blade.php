@php
    $disable_actions = $app_is_demo && (isset($category) && ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image'));
    $providers = [
    'anthropic' => 'Anthropic',
    'openai' => 'OpenAI',
    'gemini' => 'Gemini'
    ];

    $models = [
        'openai' => [
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            'gpt-4o' => 'GPT-4 Omni'
        ],
        'anthropic' => [
            'claude-3-haiku-20240307' => 'Claude Haiku',
            'claude-3-5-sonnet-20240620' => 'Claude Sonnet 3.5'
        ],
        'gemini' => [
            'gemini-1.5-pro-latest' => 'Gemini 1.5 Pro',
            'gemini-1.5-flash' => 'Gemini 1.5 Flash'
        ]
    ];
@endphp

<x-card
    class="chats-list-container flex h-[inherit] w-full shrink-0 grow-0 flex-col overflow-hidden rounded-e-none border-e-0 max-md:absolute max-md:start-0 max-md:top-20 max-md:z-50 max-md:h-0 max-md:overflow-hidden max-md:border-none max-md:bg-background/95 max-md:backdrop-blur-lg max-md:backdrop-saturate-150 max-md:transition-all max-md:duration-300 md:!flex [&.active]:h-[calc(100%-80px)]"
    class:body="flex flex-col h-full"
    id="chats-list-container"
    size="none"
    ::class="{ 'active': mobileSidebarShow }"
>
    <div class="chats-search h-20 border-b p-5 max-xl:p-2.5">
        <form
            class="chats-search-form relative"
            action="#"
        >
            <x-forms.input
                class="navbar-search-input peer rounded-full border-clay bg-clay ps-10"
                id="chat_search_word"
                data-category-id="{{ $category->id }}"
                type="search"
                onkeydown="return event.key != 'Enter';"
                placeholder="{{ __('Search') }}"
                aria-label="{{ __('Search in website') }}"
            />
            <x-tabler-search class="size-5 pointer-events-none absolute start-3 top-1/2 -translate-y-1/2 opacity-80" />
        </form>
    </div>
    <div
        class="chats-list grow-0 overflow-hidden"
        id="chat_sidebar_container"
    >
        @if (view()->hasSection('chat_sidebar_list'))
            @yield('chat_sidebar_list')
        @else
            @include('panel.user.openai_chat.components.chat_sidebar_list')
        @endif
    </div>
    <div class="chats-new mt-auto px-6 py-8 max-xl:hidden">
        @if (view()->hasSection('chat_sidebar_actions'))
            @yield('chat_sidebar_actions')
        @else
            @if (isset($category) && $category->slug == 'ai_pdf')
                <input
                    id="selectDocInput"
                    type="file"
                    style="display: none;"
                    accept=".pdf, .csv, .docx"
                />
                <x-button
                    class="lqd-upload-doc-trigger w-full text-xs"
                    href="javascript:void(0);"
                    onclick="return $('#selectDocInput').click();"
                >
                    <x-tabler-plus class="size-5" />
                    {{ __('Upload Document') }}
                </x-button>
                @if ($plan != '')
                <h2>{{ __('Modelos') }} </h2>
                <x-forms.input
                    id="provider"
                    size="sm"
                    type="select"
                    label="{{ __('Fornecedor') }}"
                    containerClass="w-36 mt-5"
                    name="provider"
                    onchange="updateModels()"
                    required
                >
                    @foreach ($providers as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-forms.input>

                <!-- Model Dropdown -->
                <x-forms.input
                    id="model"
                    size="sm"
                    type="select"
                    label="{{ __('Model') }}"
                    containerClass="w-36 mt-2"
                    name="model"
                    required
                >
                    <!-- Options will be populated dynamically -->
                </x-forms.input>
                @else
                    <input id="provider" type="hidden" value=" ">
                    <input id="model" type="hidden" value=" ">
                @endif
            @else
                <x-button
                    class="lqd-new-chat-trigger w-full text-xs mb-3"
                    href="javascript:void(0);"
                    onclick="{!! $disable_actions
                        ? 'return toastr.info(\'{{ __('This feature is disabled in Demo version.') }}\')'
                        : 'return startNewChat(\'{{ $category->id }}\', \'{{ LaravelLocalization::getCurrentLocale() }}\')' !!}"
                >
                    <x-tabler-plus class="size-5" />
                    {{ __('New Conversation') }}
                </x-button>
                @if ($plan != '')
                <h2>{{ __('Modelos') }} </h2>
                <x-forms.input
                    id="provider"
                    size="sm"
                    type="select"
                    label="{{ __('Fornecedor') }}"
                    containerClass="w-36 mt-5"
                    name="provider"
                    onchange="updateModels()"
                    required
                >
                    @foreach ($providers as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-forms.input>

                <!-- Model Dropdown -->
                <x-forms.input
                    id="model"
                    size="sm"
                    type="select"
                    label="{{ __('Model') }}"
                    containerClass="w-36 mt-2"
                    name="model"
                    required
                >
                    <!-- Options will be populated dynamically -->
                </x-forms.input>
                @endif

            @endif
        @endif
    </div>
</x-card>
<script>
// Passando os dados do PHP para o JavaScript de forma segura
var modelsData = @json($models);

function updateModels() {
    if ( document.getElementById('provider').value == ' ') {
        return
    }
    var provider = document.getElementById('provider').value;
    var modelSelect = document.getElementById('model');
    modelSelect.innerHTML = '';

    if (provider != null && modelSelect != null && modelsData != null && modelsData[provider] != null) {
        for (var value in modelsData[provider]) {
            var option = new Option(modelsData[provider][value], value);
            modelSelect.add(option);
        }
    }
}

// Inicializa os modelos quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
    updateModels();
});
</script>