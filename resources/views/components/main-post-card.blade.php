<a href="{{ route('post.show', $post->slug) }}" class="read_post">
<div class="post">
    @if($post->is_highlighted)
        <div class="highlighted_icon">Wyróżnione <i class="fa-solid fa-star"></i></div>
    @endif
    <img src="{{ asset($post->image_path) }}" alt="{{ $post->title }}">
    <div class="read"><i class="fa-solid fa-angles-right"></i>Przeczytaj</div>
    <div class="body">
        <div class="top-info">
            @if ($post->category)
                <div class="category" style="background: {{ $post->category->backgroundColor }}CC; color: {{ $post->category->textColor }}">{{ $post->category->name }}</div>
            @endif
            @if ($post->read_time)
                <i class="fa-solid fa-clock"></i>
                <p class="reading-time">{{ $post->read_time }} min</p>
            @endif
        </div>
        <p class="title">{{ $post->title }}</p>
        <div class="user">
            <img src="{{ asset($post->user->image_path) }}" alt="user">
            <p><span class="name">{{ $post->user->firstname . ' ' . $post->user->lastname }}</span><br><span class="date"> {{ \Carbon\Carbon::parse($post->created_at)->translatedFormat('d F, Y') }}</span></p>
        </div>
        <p class="short_body">{{ $post->excerpt }}</p>
    </div>
</div>
</a>
