<x-admin-layout :edit="true">
    @section('scripts')
        <script src="//cdn.quilljs.com/1.3.6/quill.js"></script>
        <link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        @vite(['resources/js/post.js'])
        @vite(['resources/js/editPost.js'])
    @endsection

    <header class="header_post_edit">
        <a href="{{ route('posts.index') }}"><i class="fa-solid fa-left-long"></i> Powrót</a>
        <div class="edit_post_actions">
            <a href="{{ route('history.index', $post->id) }}" class="history"><span class="text">Historia</span><span class="icon"><i class="fa-solid fa-timeline"></i></span></a>
            <div class="submit" onClick="submitForm();"><span class="text">Opublikuj</span><span class="icon"><i class="fa-solid fa-upload"></i></span></div>
        </div>
        <div class="profile">
            <img src="{{ asset(Auth::user()->image_path) }}" alt="" class="profile_img">
            <i class="fa-solid fa-angles-down"></i>
            <span class="notifications_count">{{ $unreadNotifications }}</span>
        </div>
    </header>

    <div class="post__create post__edit">
        <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data" id="form">
            @csrf
            @method('PATCH')
            <div id="content" data-image-url="{{route('images.store')}}">
            <div class="post_container">
                @if(count($errors) > 0)
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                <div class="top">
                    <div class="image">
                        <img src="{{ asset($post->image_path) }}" id="output" alt="image">
                        <input id="image" type="hidden" name="image">
                        <div class="change_image"><i class="fa-solid fa-image"></i> Zmień</div>
                    </div>
                    <div class="info">
                        <p class="info_title_length">Maksymalnie 255 znaków. <span class='current_title_length'>{{ Str::length($post->title) }}/255</span></p>
                        <input type="text" name="title" class="title" autocomplete="off" value="{{ $post->title }}">
                        <div class="reading-info">
                            <p class="reading-text">Czas czytania: </p>
                            <i class="fa-solid fa-clock"></i>
                            <p class="reading-time">{{ $post->read_time ? $post->read_time : 0 }} min</p>
                            <button type="button" class="calculate" onclick="calculateReadTime();">Przelicz</button>
                        </div>
                        <p class="date">{{ $post->updated_at->format('d.m.Y') }} by {{ $post->user->firstname . ' ' . $post->user->lastname }}</p>
                    </div>
                </div>
            </div>
            <div class="post_body">
                <div id="editor">

                </div>

                <textarea name="body" style="display: none" id="hiddenArea">{!!$post->body !!}</textarea>

                <div class="actions">
                    <a><i class="fa-solid fa-arrow-left"></i> Powrót do strony głównej</a>
                    <a>Następny post <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="post_options">
                <div class="header">Dodatkowe opcje:</div>
                <label>Krótki opis</label>
                <p class="info excerpt_length">Maksymalnie 510 znaków. <span class='current_excerpt_length'>{{ Str::length($post->excerpt) }}/510</span></p>
                <textarea name="excerpt">{{ $post->excerpt }}</textarea>
                <label>Widoczność</label>
                <div class="published">
                    <p>Ustaw widoczność na publiczne</p>
                    <label class="switch">
                        <input type="checkbox" name="is_published" {{ $post->is_published == true ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>
                <label>Kategoria</label>
                @isset($post)
                    @isset($post->category)
                        <div class="category-selected" style="border: none; background: {{ $post->category->backgroundColor }}CC; color: {{ $post->category->textColor }}">{{ $post->category->name }}</div>
                    @else
                        <div class="category-selected">Nie wybrano</div>
                    @endisset
                @else
                    <div class="category-selected">Nie wybrano</div>
                @endisset
                <p class="categories_extend" onclick="categoriesToggle();">Ukryj <i class="fa-solid fa-chevron-up"></i></p>
                <div class="categories_list">
                    @foreach($categories as $category)
                        <div class="category" style="background: {{ $category->backgroundColor }}CC; color: {{ $category->textColor }}" onclick="changeToCategory(event, {{ $category->id }})" data-id="{{ $category->id }}">{{ $category->name }}</div>
                    @endforeach
                </div>
                <input type="hidden" name="category_id" value="{{ isset($post) ? ($post->category ? $post->category->id : 0) : 0 }}"/>
                <div class="auto-save-info">

                </div>
            </div>
        </form>
        <x-select-image />
    </div>
    @if ($hasAutoSave)
        <script type="module">
            detectedAutoSave();
        </script>
    @endif
</x-admin-layout>
