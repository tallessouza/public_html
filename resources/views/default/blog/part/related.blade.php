@if($relatedPosts)
    <h3 class="text-center text-[25px] mt-32 mb-16">{{__('You may also like')}}</h3>
    <div class="flex w-2/3 mx-auto gap-10 flex-col md:flex-row">
        @foreach ($relatedPosts as $post)
            @include('blog.part.card')
        @endforeach
    </div>
@endif