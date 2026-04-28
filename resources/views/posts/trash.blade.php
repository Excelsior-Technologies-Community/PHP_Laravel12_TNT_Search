<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Trash Posts</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen">

    <div class="max-w-5xl mx-auto py-10 px-4">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">🗑 Trash Posts</h1>

            <a href="/" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Back to Home
            </a>
        </div>

        <!-- SUCCESS MESSAGE -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-100 text-green-700 shadow">
                {{ session('success') }}
            </div>
        @endif

        <!-- Content -->
        <div class="space-y-4">

            @forelse($posts as $post)
                <div class="bg-white shadow-md rounded-xl p-5 flex justify-between items-center">

                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">
                            {{ $post->title }}
                        </h2>
                        <p class="text-gray-500 text-sm">
                            Deleted at: {{ $post->deleted_at }}
                        </p>
                    </div>

                    <div class="flex gap-3">

                        <a href="/restore/{{ $post->id }}"
                            class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                            Restore
                        </a>

                        <form method="POST" action="/force-delete/{{ $post->id }}">
                            @csrf
                            @method('DELETE')

                            <button onclick="return confirm('Delete permanently?')"
                                class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                                Delete
                            </button>
                        </form>

                    </div>

                </div>
            @empty
                <div class="text-center text-gray-500 py-10">
                    No posts in trash 💤
                </div>
            @endforelse

        </div>

    </div>

</body>

</html>