<!DOCTYPE html>
<html>

<head>
    <title>Posts</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen">

    <div class="max-w-6xl mx-auto py-10 px-4">

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

        @if(session('success'))
            <div id="successMsg" class="mb-6 p-4 rounded-xl bg-green-100 text-green-700 shadow">
                {{ session('success') }}
            </div>
        @endif

        <div id="ajaxMsg" class="hidden mb-6 p-4 rounded-xl bg-green-100 text-green-700 shadow">
        </div>

        <div class="bg-white rounded-2xl shadow p-5 mb-8 position-relative">
            <form method="GET" id="searchForm" class="flex flex-wrap gap-3 relative">

                <div class="flex-1 relative">
                    <input name="search" id="liveSearchInput" value="{{ request('search') }}" placeholder="🔍 Search posts..." autocomplete="off"
                        class="w-100 w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                    
                    <div id="searchSuggestions" class="absolute left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-lg hidden z-50 overflow-hidden max-h-60 overflow-y-auto">
                    </div>
                </div>

                <select name="status" id="statusSelect" class="border border-gray-300 rounded-xl px-4 py-2">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl">
                    Search
                </button>

            </form>
        </div>

        <div id="postsContainer" class="grid md:grid-cols-2 gap-6">
            @forelse($posts as $post)
                <div id="post-{{ $post->id }}" class="bg-white rounded-2xl shadow hover:shadow-xl transition p-6">

                    <h2 class="text-xl font-semibold text-gray-800 mb-2 post-title">
                        <a href="/post/{{ $post->slug }}" class="hover:text-blue-600">
                            {!! request('search') ? preg_replace('/(' . preg_quote(request('search'), '/') . ')/i', '<mark class="bg-yellow-300 text-dark px-1 rounded">$1</mark>', $post->title) : $post->title !!}
                        </a>
                    </h2>

                    <p class="text-gray-600 mb-4 post-body">
                        {!! request('search') ? preg_replace('/(' . preg_quote(request('search'), '/') . ')/i', '<mark class="bg-yellow-300 text-dark px-1 rounded">$1</mark>', \Illuminate\Support\Str::limit($post->body, 100)) : \Illuminate\Support\Str::limit($post->body, 100) !!}
                    </p>

                    <div class="mb-4">
                        <span class="status-badge px-3 py-1 rounded-full text-sm
                        {{ $post->status ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $post->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">

                        <button class="toggle bg-yellow-400 hover:bg-yellow-500 text-sm px-4 py-1 rounded-lg"
                            data-id="{{ $post->id }}">
                            Toggle
                        </button>

                        <form method="POST" action="/delete/{{ $post->id }}" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 hover:underline text-sm">
                                Delete
                            </button>
                        </form>

                    </div>

                </div>
            @empty
                <div id="noPostsMsg" class="col-span-2 text-center text-gray-500 py-10">
                    No posts found 😔
                </div>
            @endforelse
        </div>

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

    <script>
        $(document).on('click', '.toggle', function () {
            let id = $(this).data('id');
            let button = $(this);

            $.post('/status', {
                _token: '{{ csrf_token() }}',
                id: id
            }, function (res) {
                $('#ajaxMsg').text('Status updated successfully!').removeClass('hidden').fadeIn();
                setTimeout(() => { $('#ajaxMsg').fadeOut(); }, 2000);

                let badge = button.closest('.bg-white').find('.status-badge');
                if (badge.text().trim() === 'Active') {
                    badge.text('Inactive').removeClass('bg-green-100 text-green-700').addClass('bg-red-100 text-red-700');
                } else {
                    badge.text('Active').removeClass('bg-red-100 text-red-700').addClass('bg-green-100 text-green-700');
                }
            });
        });

        function toggleTrash() {
            $('#trashSection').slideToggle();
        }

        setTimeout(() => {
            $('#successMsg').fadeOut();
        }, 3000);

        function getSearchHistory() {
            let history = localStorage.getItem('search_history');
            return history ? JSON.parse(history) : [];
        }

        function saveSearchToHistory(query) {
            if (!query) return;
            let history = getSearchHistory();
            history = history.filter(item => item !== query);
            history.unshift(query);
            if (history.length > 5) history.pop();
            localStorage.setItem('search_history', JSON.stringify(history));
        }

        function removeHistoryItem(e, item) {
            e.preventDefault();
            e.stopPropagation();
            let history = getSearchHistory();
            history = history.filter(i => i !== item);
            localStorage.setItem('search_history', JSON.stringify(history));
            $('#liveSearchInput').trigger('input');
        }

        $(document).ready(function() {
            const searchInput = $('#liveSearchInput');
            const suggestionsBox = $('#searchSuggestions');
            const searchForm = $('#searchForm');
            const statusSelect = $('#statusSelect');

            function performLiveSearch() {
                let query = searchInput.val().trim();
                let status = statusSelect.val();

                $.ajax({
                    url: "{{ url('/') }}",
                    method: "GET",
                    data: { search: query, status: status },
                    success: function(html) {
                        let newPosts = $(html).find('#postsContainer').html();
                        $('#postsContainer').html(newPosts);
                        
                        const url = new URL(window.location);
                        if (query) url.searchParams.set('search', query);
                        else url.searchParams.delete('search');
                        if (status) url.searchParams.set('status', status);
                        else url.searchParams.delete('status');
                        window.history.pushState({}, '', url);
                        
                        suggestionsBox.addClass('hidden');
                    }
                });
            }

            searchInput.on('input', function() {
                let query = $(this).val().trim();

                if (query.length === 0) {
                    let history = getSearchHistory();
                    if (history.length > 0) {
                        suggestionsBox.empty();
                        history.forEach(function(item) {
                            let historyItem = $(`
                                <div class="px-4 py-2 hover:bg-gray-100 text-gray-700 border-b border-gray-100 last:border-0 flex items-center justify-between cursor-pointer history-select-item" data-query="${item}">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400">🕒</span>
                                        <span>${item}</span>
                                    </div>
                                    <button class="text-gray-400 hover:text-red-500 text-sm p-1 remove-history-btn" data-item="${item}">&times;</button>
                                </div>
                            `);
                            suggestionsBox.append(historyItem);
                        });
                        suggestionsBox.removeClass('hidden');
                    } else {
                        suggestionsBox.addClass('hidden').empty();
                    }
                    return;
                }

                if (query.length < 2) {
                    suggestionsBox.addClass('hidden').empty();
                    return;
                }

                $.ajax({
                    url: "{{ route('posts.suggestions') }}",
                    method: "GET",
                    data: { q: query },
                    success: function(data) {
                        suggestionsBox.empty();
                        let history = getSearchHistory();
                        let filteredHistory = history.filter(item => item.toLowerCase().includes(query.toLowerCase()));

                        if (filteredHistory.length > 0) {
                            filteredHistory.forEach(function(item) {
                                let historyItem = $(`
                                    <div class="px-4 py-1 bg-gray-50 hover:bg-gray-100 text-gray-600 border-b border-gray-100 flex items-center justify-between cursor-pointer history-select-item" data-query="${item}">
                                        <div class="flex items-center gap-2 text-sm">
                                            <span class="text-gray-400">🕒</span>
                                            <span>${item}</span>
                                        </div>
                                        <button class="text-gray-400 hover:text-red-500 text-sm p-1 remove-history-btn" data-item="${item}">&times;</button>
                                    </div>
                                `);
                                suggestionsBox.append(historyItem);
                            });
                        }

                        if (data.length > 0) {
                            data.forEach(function(item) {
                                let cleanTitle = item.title.replace(/<\/?[^>]+(>|$)/g, "");
                                let suggestionItem = $(`
                                    <a href="${item.url}" class="block px-4 py-2 hover:bg-gray-100 text-gray-700 border-b border-gray-100 last:border-0 flex items-center gap-2 suggestion-link" data-title="${cleanTitle}">
                                        <span class="text-gray-400">🔍</span>
                                        <span>${item.title}</span>
                                    </a>
                                `);
                                suggestionsBox.append(suggestionItem);
                            });
                            suggestionsBox.removeClass('hidden');
                        } else if (filteredHistory.length === 0) {
                            suggestionsBox.append('<div class="px-4 py-2 text-gray-500 text-sm">No suggestions found</div>').removeClass('hidden');
                        }
                    }
                });
            });

            searchInput.on('focus', function() {
                if ($(this).val().trim().length === 0) {
                    $(this).trigger('input');
                }
            });

            $(document).on('click', '.history-select-item', function(e) {
                if ($(e.target).hasClass('remove-history-btn')) return;
                let selectedQuery = $(this).data('query');
                searchInput.val(selectedQuery);
                suggestionsBox.addClass('hidden');
                saveSearchToHistory(selectedQuery);
                performLiveSearch();
            });

            $(document).on('click', '.remove-history-btn', function(e) {
                let item = $(this).data('item');
                removeHistoryItem(e, item);
            });

            $(document).on('click', '.suggestion-link', function() {
                let title = $(this).data('title');
                saveSearchToHistory(title);
            });

            searchForm.on('submit', function(e) {
                e.preventDefault();
                let query = searchInput.val().trim();
                saveSearchToHistory(query);
                suggestionsBox.addClass('hidden');
                performLiveSearch();
            });

            statusSelect.on('change', function() {
                performLiveSearch();
            });

            $(document).on('click', function(e) {
                if (!searchInput.is(e.target) && !suggestionsBox.is(e.target) && suggestionsBox.has(e.target).length === 0) {
                    suggestionsBox.addClass('hidden');
                }
            });
        });
    </script>
</body>

</html>