<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        if ($search) {
            $posts = Post::search($search)->get();
        } else {
            $posts = Post::latest()->get();
        }

        if ($request->status !== null && $request->status !== '') {
            $posts = $posts->where('status', $request->status);
        }

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        Post::create($request->all());

        return redirect('/')->with('success', 'Post created successfully!');
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        return view('posts.show', compact('post'));
    }

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

    public function destroy($id)
    {
        Post::findOrFail($id)->delete();
        return back()->with('success', 'Post moved to trash!');
    }

    public function trash()
    {
        $posts = Post::onlyTrashed()->get();
        return view('posts.trash', compact('posts'));
    }

    public function restore($id)
    {
        Post::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Post restored successfully!');
    }

    public function forceDelete($id)
    {
        Post::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Post deleted permanently!');
    }

    public function suggestions(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return response()->json([]);
        }

        $posts = Post::search($query)->get()->take(5);

        $formattedPosts = $posts->map(function ($post) use ($query) {
            $highlightedTitle = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-300 text-dark px-1 rounded">$1</mark>', $post->title);

            return [
                'id' => $post->id,
                'title' => $highlightedTitle,
                'url' => '/post/' . $post->slug
            ];
        });

        return response()->json($formattedPosts);
    }
}