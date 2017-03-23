<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Google\Cloud\Trace\RequestTracer;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index', 'show');
    }

    public function index()
    {
        $posts = RequestTracer::instrument(['name' => 'db/posts/latest'], function() {
            return Post::latest()->get();
        });
        return RequestTracer::instrument(['name' => 'view'], function () use ($posts) {
            return view('posts.index', compact('posts'));
        });
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store()
    {
        $this->validate(request(), [
            'title' => 'required',
            'body' => 'required'
        ]);

        $permalink = preg_replace('/\W/', '-', request('title'));
        $post = Post::create([
            'title' => request('title'),
            'body' => request('body'),
            'user_id' => auth()->id(),
            'permalink' => $permalink
        ]);

        return redirect("/posts/{$post->permalink}");
    }
}
