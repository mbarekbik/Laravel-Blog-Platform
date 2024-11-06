<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\CommentNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:comment-list', ['only' => ['index']]);
        $this->middleware('permission:comment-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:comment-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        if ($request->input('q') !== null) {
            $terms = $request->input('q');
        } else {
            $terms = '';
        }
        if ($request->input('order') !== null) {
            $order = $request->input('order');
        } else {
            $order = 'desc';
        }
        if ($request->input('limit') !== null) {
            $limit = $request->input('limit');
        } else {
            $limit = 20;
        }
        if ($request->input('users') !== null && $request->input('users')[0] !== null) {
            if (isset($request->input('users')[1])) {
                $temp = $request->input('users');
            } else {
                $temp = explode(',', $request->input('users')[0]);
            }
            $selected_users_array = $temp;
            $selected_users = User::whereIn('id', $selected_users_array)->get()->toArray();
        } else {
            $selected_users = null;
            $selected_users_array = null;
        }

        if (Auth::User()->hasPermissionTo('comment-super-list')) {
            if ($selected_users_array) {
                $comments = Comment::join('posts', 'posts.id', '=', 'comments.post_id')
                    ->whereIn('posts.user_id', $selected_users_array)
                    ->orderBy('comments.id', $order)
                    ->select('comments.*');
            } else {
                $comments = Comment::orderBy('id', $order);
            }
        } else {
            $comments = Comment::join('posts', 'posts.id', '=', 'comments.post_id')
                ->where('posts.user_id', Auth::id())
                ->orderBy('comments.id', $order)
                ->select('comments.*');
        }

        $users = User::all();

        if ($terms !== null && $terms !== '') {
            $keywords = explode(' ', $terms);

            $comments->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'like', '%' . $keyword . '%')
                            ->orWhere('body', 'like', '%' . $keyword . '%');
                }
            });
        }

        if ((int)$limit === 0) {
            $comments = $comments->get();
        } else {
            $comments = $comments->paginate($limit);
        }

        return view('comment.index', [
            'comments' => $comments,
            'users' => $users,
            'terms' => $terms,
            'order' => $order,
            'limit' => $limit,
            'selected_users' => $selected_users,
            'selected_users_array' => $selected_users_array,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $path = parse_url($request->headers->get('referer'), PHP_URL_PATH);
        $post_slug = explode('/', $path)[2];

        $post = Post::where('slug', $post_slug)->firstOrFail();

        $request->validate([
            'name' => 'required',
            'body' => 'required',
        ]);

        $comment = Comment::create([
            'name' => $request->name,
            'body' => $request->body,
            'post_id' => $post->id,
        ]);

        if (Auth::check() && Auth::Id() !== $post->user_id) {
            $post->user->notify(new CommentNotification('INFO', 'Pojawił się nowy komentarz.', "/post/$post_slug"));
        } elseif (!Auth::check()) {
            $post->user->notify(new CommentNotification('INFO', 'Pojawił się nowy komentarz.', "/post/$post_slug"));
        }


        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit(int $id)
    {
        $comment = Comment::findOrFail($id);

        $post = Post::findOrFail($comment->post_id);

        $this->checkUserIdPost($post);

        return view('comment.edit', [
            'comment' => $comment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required',
            'body' => 'required',
        ]);

        $input = $request->all();

        $path = parse_url($request->headers->get('referer'), PHP_URL_PATH);
        $comment_id = explode('/', $path)[3];

        if ($id != $comment_id) {
            abort(403);
        }

        $comment = Comment::findOrFail($comment_id);

        $post = Post::findOrFail($comment->post_id);

        $this->checkUserIdPost($post);

        $comment->update($input);

        return redirect()->route('comments.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $comment = Comment::findOrFail($id);

        $post = Post::findOrFail($comment->post_id);

        $this->checkUserIdPost($post);

        $comment->delete();

        return redirect()->back();
    }

    private function checkUserIdPost(Post $post): void
    {
        if ($post->user_id != Auth::id() && ! Auth::User()->hasPermissionTo('comment-super-list')) {
            abort(403);
        }
    }
}
