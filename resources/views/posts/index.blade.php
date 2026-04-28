<!DOCTYPE html>
<html>

<head>
    <title>Posts</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen">

    <div class="max-w-6xl mx-auto py-10 px-4">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-4xl font-bold text-gray-800">📚 Posts Manager</h1>

            <div class="flex gap-3">
                <a href="/create" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-xl shadow">
                    + Create
                </a>

                <button onclick="toggleTrash()"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2 rounded-xl shadow">
                    Trash
                </button>
            </div>
        </div>

        <!-- SESSION SUCCESS -->
        @if(session('success'))
            <div id="successMsg" class="mb-6 p-4 rounded-xl bg-green-100 text-green-700 shadow">
                {{ session('success') }}
            </div>
        @endif

        <!-- AJAX SUCCESS -->
        <div id="ajaxMsg" class="hidden mb-6 p-4 rounded-xl bg-green-100 text-green-700 shadow">
        </div>

        <!-- SEARCH -->
        <div class="bg-white rounded-2xl shadow p-5 mb-8">
            <form method="GET" class="flex flex-wrap gap-3">

                <input name="search" value="{{ request('search') }}" placeholder="🔍 Search posts..."
                    class="flex-1 border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none">

                <select name="status" class="border border-gray-300 rounded-xl px-4 py-2">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>

                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl">
                    Search
                </button>

            </form>
        </div>

        <!-- POSTS -->
        <div class="grid md:grid-cols-2 gap-6">

            @forelse($posts as $post)
                <div id="post-{{ $post->id }}" class="bg-white rounded-2xl shadow hover:shadow-xl transition p-6">

                    <!-- TITLE -->
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">
                        <a href="/post/{{ $post->slug }}" class="hover:text-blue-600">
                            {{ $post->title }}
                        </a>
                    </h2>

                    <!-- BODY -->
                    <p class="text-gray-600 mb-4">
                        {{ \Illuminate\Support\Str::limit($post->body, 100) }}
                    </p>

                    <!-- STATUS -->
                    <div class="mb-4">
                        <span class="status-badge px-3 py-1 rounded-full text-sm
                        {{ $post->status ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $post->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- ACTIONS -->
                    <div class="flex justify-between items-center">

                        <!-- TOGGLE -->
                        <button class="toggle bg-yellow-400 hover:bg-yellow-500 text-sm px-4 py-1 rounded-lg"
                            data-id="{{ $post->id }}">
                            Toggle
                        </button>

                        <!-- DELETE -->
                        <form method="POST" action="/delete/{{ $post->id }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 hover:underline text-sm">
                                Delete
                            </button>
                        </form>

                    </div>

                </div>
            @empty
                <div class="col-span-2 text-center text-gray-500 py-10">
                    No posts found 😔
                </div>
            @endforelse

        </div>

        <!-- TRASH -->
        <div id="trashSection" class="hidden mt-12">

            <h2 class="text-2xl font-bold text-red-600 mb-6">🗑 Trash</h2>

            <div class="grid md:grid-cols-2 gap-6">

                @foreach(\App\Models\Post::onlyTrashed()->get() as $post)
                    <div class="bg-red-50 rounded-2xl shadow p-5">

                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ $post->title }}
                        </h3>

                        <p class="text-gray-500 text-sm mb-3">
                            Deleted: {{ $post->deleted_at }}
                        </p>

                        <div class="flex justify-between">

                            <a href="/restore/{{ $post->id }}"
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-1 rounded-lg text-sm">
                                Restore
                            </a>

                            <form method="POST" action="/force-delete/{{ $post->id }}">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Delete permanently?')"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded-lg text-sm">
                                    Delete Forever
                                </button>
                            </form>

                        </div>

                    </div>
                @endforeach

            </div>

        </div>

    </div>

    <!-- SCRIPT -->
    <script>

        // TOGGLE STATUS (AJAX SUCCESS)
        $('.toggle').click(function () {
            let id = $(this).data('id');
            let card = $(this).closest('.bg-white');

            $.post('/status', {
                _token: '{{ csrf_token() }}',
                id: id
            }, function (res) {

                // SHOW MESSAGE
                $('#ajaxMsg').text('Status updated successfully!')
                    .removeClass('hidden')
                    .fadeIn();

                setTimeout(() => {
                    $('#ajaxMsg').fadeOut();
                }, 2000);

                // RELOAD AFTER SMALL DELAY
                setTimeout(() => {
                    location.reload();
                }, 1000);

            });
        });

        // TRASH TOGGLE
        function toggleTrash() {
            $('#trashSection').slideToggle();
        }

        // AUTO HIDE SESSION MSG
        setTimeout(() => {
            $('#successMsg').fadeOut();
        }, 3000);

    </script>

</body>

</html>