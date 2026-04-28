<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // LIST + SEARCH + FILTER
    public function index(Request $request)
    {
        $search = $request->search;

        if ($search) {
            $posts = Post::search($search)->get();
        } else {
            $posts = Post::latest()->get();
        }

        // Status filter
        if ($request->status !== null && $request->status !== '') {
            $posts = $posts->where('status', $request->status);
        }

        return view('posts.index', compact('posts'));
    }

    //CREATE
    public function create()
    {
        return view('posts.create');
    }


    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        Post::create($request->all());

        return redirect('/')->with('success', 'Post created successfully!');

    }

    // SHOW BY SLUG
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        return view('posts.show', compact('post'));
    }

    // TOGGLE STATUS (AJAX)
    public function toggleStatus(Request $request)
{
    $post = Post::findOrFail($request->id);
    $post->status = !$post->status;
    $post->save();

    return response()->json([
        'success' => true,
        'message' => 'Status updated successfully!'
    ]);
}

    // DELETE (SOFT)
    public function destroy($id)
    {
        Post::findOrFail($id)->delete();
        return back()->with('success', 'Post moved to trash!');
    }

    // TRASH
    public function trash()
    {
        $posts = Post::onlyTrashed()->get();
        return view('posts.trash', compact('posts'));
    }

    // RESTORE
    public function restore($id)
    {
        Post::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Post restored successfully!');
    }

    // FORCE DELETE
    public function forceDelete($id)
    {
        Post::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Post deleted permanently!');
    }
}