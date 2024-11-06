<div class="comment">
    <div class="head">
        <div class="basic_info">
            <i class="fa-solid fa-caret-right"></i>
            <p>{{ $comment->name }}</p>
            <p>{{ $comment->created_at->format('d.m.Y H:i') }}</p>
        </div>
        <div class="comment_actions">
            @can('comment-edit')
                <i class="fa-solid fa-circle"></i>
                <a href="{{ route('comments.edit', $comment->id) }}" class="edit">Edytuj</a>
            @endcan
            @if(Auth::Check() && ($post->user_id == Auth::id() OR Auth::User()->hasPermissionTo('comment-super-list')))
                @can('comment-delete')
                    <i class="fa-solid fa-circle"></i>
                    <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" id="comment_delete_{{ $comment->id }}">
                        @method('DELETE')
                        @csrf
                        <div class="delete" onClick="document.getElementById('comment_delete_{{ $comment->id }}').submit()">Usuń</div>
                    </form>
                @endcan
            @endif
        </div>
    </div>
    <div class="body">
        {{ $comment->body }}
    </div>
</div>
