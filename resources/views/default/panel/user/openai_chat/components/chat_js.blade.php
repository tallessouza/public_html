<script src="{{ custom_theme_url('/assets/libs/fslightbox/fslightbox.js') }}"></script>

<script>
    var chatid = @json($list)[0]?.id;
    $(`#chat_${chatid}`).addClass('active').siblings().removeClass('active');
    const guest_id = document.getElementById("guest_id")?.value;
    const guest_search = document.getElementById("guest_search")?.value;
    const guest_search_id = document.getElementById("guest_search_id")?.value;
    const guest_event_id = document.getElementById("guest_event_id")?.value;
    const guest_look_id = document.getElementById("guest_look_id")?.value;
    const guest_product_id = document.getElementById("guest_product_id")?.value;
    const stream_type = '{!! $settings_two->openai_default_stream_server !!}';
    const category = @json($category);
    const openai_model = '{!! $setting->openai_default_model !!}';
    const prompt_prefix = document.getElementById("prompt_prefix")?.value;

    let messages = [];
    let training = [];

    @if ($chat_completions != null)
        training = @json($chat_completions);
    @endif

    messages.push({
        role: "assistant",
        content: prompt_prefix
    });

    @if ($lastThreeMessage != null)
        @foreach ($lastThreeMessage as $entry)
            message = {
                role: "user",
                content: @json($entry->input)
            };
            messages.push(message);
            message = {
                role: "assistant",
                content: @json($entry->output)
            };
            messages.push(message);
        @endforeach
    @endif
</script>
<script src="{{ custom_theme_url('/assets/js/panel/openai_chat.js?v=' . time()) }}"></script>
@if (count($list) == 0 && $category->slug != 'ai_pdf')
    <script>
        window.addEventListener("load", (event) => {
            return startNewChat({{ $category->id }}, '{{ LaravelLocalization::getCurrentLocale() }}');
        });
    </script>
@endif
