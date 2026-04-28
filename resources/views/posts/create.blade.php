<!DOCTYPE html>
<html>

<head>
    <title>Create Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-10">

    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">

        <h1 class="text-2xl mb-5 font-bold">Create Post</h1>

        <form method="POST" action="/posts" class="space-y-4">
            @csrf

            <input name="title" placeholder="Title" class="w-full border p-2 rounded">

            <textarea name="body" placeholder="Body" class="w-full border p-2 rounded"></textarea>

            <div class="flex gap-3">
                <button class="bg-green-600 text-white px-4 py-2 rounded">
                    Save
                </button>

                <a href="/" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Back
                </a>
            </div>

        </form>

    </div>

</body>

</html>