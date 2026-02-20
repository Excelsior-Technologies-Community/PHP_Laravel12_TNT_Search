<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel 12 TNTSearch</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-100 to-slate-200 min-h-screen">

<div class="max-w-5xl mx-auto py-10 px-4">

    <!-- Header -->
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-slate-800">
            Laravel 12 TNTSearch
        </h1>
        <p class="text-slate-500 mt-2">
            Fast Full-Text Search Demo with Modern UI
        </p>
    </div>


    <!-- Search Card -->
    <div class="bg-white shadow-xl rounded-2xl p-6 mb-8">
        <form method="GET" class="flex gap-3">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search posts..."
                class="flex-1 border border-slate-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <button
                class="bg-blue-600 text-white px-6 py-2 rounded-xl hover:bg-blue-700 transition">
                Search
            </button>
        </form>
    </div>


    <!-- Create Post Card -->
    <div class="bg-white shadow-xl rounded-2xl p-6 mb-10">
        <h2 class="text-xl font-semibold text-slate-700 mb-4">
            Create New Post
        </h2>

        <form method="POST" action="/posts" class="space-y-4">
            @csrf

            <input
                type="text"
                name="title"
                placeholder="Post title..."
                class="w-full border border-slate-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >

            <textarea
                name="body"
                rows="4"
                placeholder="Write something..."
                class="w-full border border-slate-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            ></textarea>

            <button
                class="bg-emerald-600 text-white px-6 py-2 rounded-xl hover:bg-emerald-700 transition">
                Add Post
            </button>
        </form>
    </div>


    <!-- Posts List -->
    <div class="space-y-6">
        @forelse($posts as $post)
            <div class="bg-white shadow-md rounded-2xl p-6 hover:shadow-xl transition">
                <h3 class="text-2xl font-semibold text-slate-800 mb-2">
                    {{ $post->title }}
                </h3>
                <p class="text-slate-600 leading-relaxed">
                    {{ $post->body }}
                </p>
            </div>
        @empty
            <div class="text-center text-slate-500 py-10">
                No posts found.
            </div>
        @endforelse
    </div>

</div>

</body>
</html>
