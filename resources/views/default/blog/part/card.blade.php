<article class="shadow-[0_2px_4px_rgba(149,146,157,0.15)] rounded-2xl w-full flex flex-col">
    <figure>
        <a href="{{ url('/blog', $post->slug) }}">
            <img class="w-full h-40 object-cover rounded-t-2xl" src="{{ custom_theme_url($post->feature_image, true) }}"
                alt="{{ $post->title }}">
        </a>
    </figure>
    <div class="p-5 min-h-[180px] flex flex-col font-medium">
        <div class="flex justify-between space-x-6 mb-3 text-black">
            <time datetime="{{ $post->updated_at }}"
                class="text-sm">{{ date('d M', strtotime($post->updated_at)) }}</time>
            <div class="border-b grow relative -top-2"></div>
            <a class="text-sm"
                href="{{ url('/blog/author', $post->user_id) }}">{{ App\Models\User::where('id', $post->user_id)->first()->name }}</a>
        </div>
        <h2 class="!text-[21px] mb-4 tracking-tight leading-[26px]"><a
                href="{{ url('/blog', $post->slug) }}">{{ $post->title }}</a></h2>
        <a class="flex items-center mt-auto text-[13px] text-black" href="{{ url('/blog', $post->slug) }}">
            {{ __('Read More') }}
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M9 6l6 6l-6 6"></path>
            </svg>
        </a>
    </div>
</article>
