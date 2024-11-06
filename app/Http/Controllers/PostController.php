<?php

namespace App\Http\Controllers;

use App\Models\HighlightPost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index()
    {
        $posts = Post::with('category')
            ->where('is_published', true)
            ->select('posts.*', \DB::raw('(SELECT COUNT(*) FROM highlight_posts WHERE post_id = posts.id) > 0 AS is_highlighted'))
            ->limit(20)
            ->orderBy('id', 'desc')->get();

        $highlighted_posts = HighlightPost::whereHas('post', function ($query) {
            $query->where('is_published', 1);
        })->get();

        return view('welcome', [
            'posts' => $posts,
            'highlighted_posts' => $highlighted_posts,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  string $slug
     * @return Factory|View
     */
    public function show(string $slug)
    {
        // $post = Post::findOrFail($id);
        $post = Post::where('slug', $slug)->firstOrFail();

        $nextPost = Post::where('id', '>', $post->id)->first();

        $user = User::find($post->user_id);

        if (!$post->is_published) {
            if (Auth::User()) {
                if (Auth::User() == $user || Auth::User()->hasPermissionTo('post-super-list')) {
                } else {
                    abort(404);
                }
            } else {
                abort(404);
            }
        }

        return view('post.show', [
            'post' => $post,
            'nextPost' => $nextPost,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function get(Request $request)
    {
        if ($request->input('offset')) {
            $offset = $request->input('offset');
            $posts = Post::with([
                    'category' => function ($query) {
                        $query->select('id', 'name', 'backgroundColor', 'textColor');
                    },
                    'user' => function ($query) {
                        $query->select('id', 'firstname', 'lastname', 'image_path');
                    },
                ])
                ->where('is_published', true)
                ->select('posts.*', \DB::raw('(SELECT COUNT(*) FROM highlight_posts WHERE post_id = posts.id) > 0 AS is_highlighted'))
                ->offset($offset)
                ->limit(20)
                ->orderBy('id', 'desc')->get()
                ->makeHidden(['additional_info', 'category_id', 'user_id', 'change_user_id', 'changelog', 'id', 'created_at', 'updated_at', 'is_published']);
            foreach ($posts as $post) {
                $post->category->makeHidden('id');
                $post->user->makeHidden('id');
                $post->created_at_formatted = \Carbon\Carbon::parse($post->created_at)->translatedFormat('d F, Y');
            }
            return response()->json($posts);
        } else {
            return response()->json('Not Acceptable', 406);
        }
    }
}
