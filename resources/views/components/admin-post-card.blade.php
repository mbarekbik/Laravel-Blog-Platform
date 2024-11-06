<div class="post">
    <img src="{{ asset($post->image_path) }}" alt="{{ $post->title }}">
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
{{--        <p class="date">{{ $post->updated_at->format('d.m.Y') }} by {{ $post->user->firstname . ' ' . $post->user->lastname }}</p>--}}
    </div>
    <div class="actions">
        <a href="{{ route('post.show', $post->slug) }}" class="read_more">Przejdź <i class="fa-solid fa-angles-right"></i></a>
        @can('post-edit')
            <a href="{{ route('posts.edit', $post->id) }}" class="edit">Edytuj <i class="fa-solid fa-pen-to-square"></i></a>
        @endcan
        @can('post-delete')
            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" id="post_{{ $post->id }}">
                @method('DELETE')
                @csrf
            </form>
            <button onClick="confirmDelete({{ $post->id }}, 'post')" class="delete">Usuń <i class="fa-solid fa-trash"></i></button>
        @endcan
        @can('post-highlight')
            @if($post->is_highlighted)
                <form action="{{ route('post.highlight') }}" method="POST" id="highlight_{{ $post->id }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $post->id }}">
                </form>
                <button onClick="document.getElementById('highlight_{{ $post->id }}').submit();" class="highlight deselect">Odznacz <i class="fa-solid fa-star"></i></button>
            @else
                @if($countHighlighted < 3)
                    <form action="{{ route('post.highlight') }}" method="POST" id="highlight_{{ $post->id }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $post->id }}">
                    </form>
                    <button onClick="document.getElementById('highlight_{{ $post->id }}').submit();" class="highlight">Wyróżnij <i class="fa-solid fa-star"></i></button>
                @endif
            @endif
        @endcan
    </div>
</div>
