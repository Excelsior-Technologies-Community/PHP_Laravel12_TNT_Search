<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-100 to-slate-200 min-h-screen">

<div class="max-w-3xl mx-auto py-12 px-4">

    <!-- Back Button -->
    <a href="/"
       class="inline-block mb-6 text-blue-600 hover:underline">
        ← Back to Posts
    </a>

    <!-- Post Card -->
    <div class="bg-white shadow-xl rounded-2xl p-8">

        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-800 mb-4">
            {{ $post->title }}
        </h1>

        <!-- Meta -->
        <div class="text-sm text-gray-400 mb-6">
            Created: {{ $post->created_at->format('d M Y') }}
        </div>

        <!-- Body -->
        <p class="text-gray-700 leading-relaxed text-lg">
            {{ $post->body }}
        </p>

        <!-- Status Badge -->
        <div class="mt-6">
            @if($post->status)
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                    Active
                </span>
            @else
                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                    Inactive
                </span>
            @endif
        </div>

    </div>

</div>

</body>
</html>