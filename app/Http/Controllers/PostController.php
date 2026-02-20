<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display posts or search results
     */
    public function index(Request $request)
    {
        $query = $request->input('q');

        $posts = $query
            ? Post::search($query)->get()
            : Post::latest()->get();

        return view('posts.index', compact('posts'));
    }

    /**
     * Store new post
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body'  => 'required',
        ]);

        Post::create($request->all());

        return redirect()->back()->with('success', 'Post created!');
    }
}
