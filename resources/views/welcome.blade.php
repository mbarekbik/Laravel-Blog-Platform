<x-main-layout>
    @section('scripts')
        @vite(['resources/js/loadPosts.js'])
    @endsection
    <div class="article">
        <div class="image__container{{ $highlighted_posts->isEmpty() ? '' : ' highlighted' }}">
            @if($highlighted_posts->isEmpty())
                <div class="text">
                    <p>Witaj na Blogu!</p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi rhoncus varius placerat. Praesent erat tellus, mattis ac finibus at, mollis in arcu. Nam malesuada libero vitae nulla pharetra sodales. Sed gravida nibh vel eros auctor, sit amet bibendum dui pharetra. Mauris iaculis sapien nisl, sit amet consequat odio consequat in. Curabitur ultrices ligula in ligula varius, ac viverra est finibus. Cras convallis et felis vitae convallis. Ut blandit ornare elementum. Praesent dapibus maximus vestibulum.</div>
                <img src="{{ asset('images/picture3.jpg') }}" alt="Main">
            @else
                <div class="highlighted_icon">Wyróżnione <i class="fa-solid fa-star"></i></div>
                @foreach($highlighted_posts as $highlighted_post)
                    <a href="{{ route('post.show', $highlighted_post->post->slug) }}">
                        <div class="post post-highlighted fade">
                            <img src="{{ asset($highlighted_post->post->image_path) }}" alt="{{ $highlighted_post->post->title }}">
                            <div class="body">
                                <div class="top-info">
                                    @if ($highlighted_post->post->category)
                                        <div class="category" style="background: {{ $highlighted_post->post->category->backgroundColor }}CC; color: {{ $highlighted_post->post->category->textColor }}">{{ $highlighted_post->post->category->name }}</div>
                                    @endif
                                    @if ($highlighted_post->post->read_time)
                                        <i class="fa-solid fa-clock"></i>
                                        <p class="reading-time">{{ $highlighted_post->post->read_time }} min</p>
                                    @endif
                                </div>
                                <p class="title">{{ $highlighted_post->post->title }}</p>
                                <div class="user">
                                    <img src="{{ asset($highlighted_post->post->user->image_path) }}" alt="user">
                                    <p><span class="name">{{ $highlighted_post->post->user->firstname . ' ' . $highlighted_post->post->user->lastname }}</span><br><span class="date"> {{ \Carbon\Carbon::parse($highlighted_post->post->created_at)->translatedFormat('d F, Y') }}</span></p>
                                </div>
                                <p class="short_body">{{ $highlighted_post->post->excerpt }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            @endif
            <div class="dots">
                @for($i = 0; $i < count($highlighted_posts); $i++)
                    <i class="dot fa-regular fa-circle" onclick="currentSlide({{$i+1}});"></i>
                @endfor
            </div>
        </div>

        <div class="container">
            <div class="posts">
                @foreach ($posts as $post)
                    <x-main-post-card :post="$post" />
                @endforeach
            </div>
            <div class="loading hidden">
                <div class="loader"></div>
            </div>
            <div class="load-posts"></div>
        </div>
    </div>
    @if(!$highlighted_posts->isEmpty())
        @vite(['resources/js/highlight.js'])
    @endif
</x-main-layout>
