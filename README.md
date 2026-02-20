# PHP_Laravel12_TNT_Search

## Project Introduction

PHP_Laravel12_TNT_Search is a simple demonstration project that integrates Laravel Scout with the TNTSearch driver in a Laravel 12 application.

The goal of this project is to implement fast, local full-text search without relying on external services such as Algolia or Meilisearch.

TNTSearch works entirely within the Laravel application using a local SQLite index, making it lightweight, cost-effective, and ideal for small to medium projects.

---

## Project Overview

This project demonstrates:

- Integration of Laravel Scout with TNTSearch

- Local full-text search indexing

- Creating searchable Eloquent models

- Importing database records into search index

- Rebuilding search index when needed

- Handling common storage and permission issues

- Building a clean modern UI using Tailwind CSS

---

## Requirements

- PHP >= 8.2
- Composer
- MySQL
- Laravel 12
- Node.js (optional, if using frontend tooling)

---

# Step-by-Step Implementation

## Step 1 — Create Laravel 12 Project

Run:

```bash
composer create-project laravel/laravel PHP_Laravel12_TNT_Search "12.*"
cd PHP_Laravel12_TNT_Search
```

Start development server (optional):

```bash
php artisan serve
```

---

## Step 2 — Install Laravel Scout

Laravel Scout provides the **search abstraction layer**.

```bash
composer require laravel/scout
```

Publish Scout configuration:

```bash
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

This creates:

```
config/scout.php
```

---

## Step 3 — Install TNTSearch Driver

TNTSearch is a **local full-text search engine** that works **without external APIs**.

```bash
composer require teamtnt/laravel-scout-tntsearch-driver
```

No API keys required.

---

## Step 4 — Configure Environment

Open **.env** and set:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tnt_search
DB_USERNAME=root
DB_PASSWORD=

SCOUT_DRIVER=tntsearch
```

After Run Migration Command:

```bash
php artisan migrate
```

---

## Step 5 — Verify Scout Configuration

Open:

```
config/scout.php
```

Ensure:

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Search Engine
    |--------------------------------------------------------------------------
    |
    | This option controls the default search connection that gets used while
    | using Laravel Scout. This connection is used when syncing all models
    | to the search service. You should adjust this based on your needs.
    |
    | Supported: "algolia", "meilisearch", "typesense",
    |            "database", "collection", "null"
    |
    */

    'driver' => env('SCOUT_DRIVER', 'tntsearch'),

    /*
    |--------------------------------------------------------------------------
    | Index Prefix
    |--------------------------------------------------------------------------
    |
    | Here you may specify a prefix that will be applied to all search index
    | names used by Scout. This prefix may be useful if you have multiple
    | "tenants" or applications sharing the same search infrastructure.
    |
    */

    'prefix' => env('SCOUT_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Queue Data Syncing
    |--------------------------------------------------------------------------
    |
    | This option allows you to control if the operations that sync your data
    | with your search engines are queued. When this is set to "true" then
    | all automatic data syncing will get queued for better performance.
    |
    */

    'queue' => env('SCOUT_QUEUE', false),

    /*
    |--------------------------------------------------------------------------
    | Database Transactions
    |--------------------------------------------------------------------------
    |
    | This configuration option determines if your data will only be synced
    | with your search indexes after every open database transaction has
    | been committed, thus preventing any discarded data from syncing.
    |
    */

    'after_commit' => false,

    /*
    |--------------------------------------------------------------------------
    | Chunk Sizes
    |--------------------------------------------------------------------------
    |
    | These options allow you to control the maximum chunk size when you are
    | mass importing data into the search engine. This allows you to fine
    | tune each of these chunk sizes based on the power of the servers.
    |
    */

    'chunk' => [
        'searchable' => 500,
        'unsearchable' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Soft Deletes
    |--------------------------------------------------------------------------
    |
    | This option allows to control whether to keep soft deleted records in
    | the search indexes. Maintaining soft deleted records can be useful
    | if your application still needs to search for the records later.
    |
    */

    'soft_delete' => false,

    /*
    |--------------------------------------------------------------------------
    | Identify User
    |--------------------------------------------------------------------------
    |
    | This option allows you to control whether to notify the search engine
    | of the user performing the search. This is sometimes useful if the
    | engine supports any analytics based on this application's users.
    |
    | Supported engines: "algolia"
    |
    */

    'identify' => env('SCOUT_IDENTIFY', false),

    /*
    |--------------------------------------------------------------------------
    | Algolia Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Algolia settings. Algolia is a cloud hosted
    | search engine which works great with Scout out of the box. Just plug
    | in your application ID and admin API key to get started searching.
    |
    */

    'algolia' => [
        'id' => env('ALGOLIA_APP_ID', ''),
        'secret' => env('ALGOLIA_SECRET', ''),
        'index-settings' => [
            // 'users' => [
            //     'searchableAttributes' => ['id', 'name', 'email'],
            //     'attributesForFaceting'=> ['filterOnly(email)'],
            // ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Meilisearch Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Meilisearch settings. Meilisearch is an open
    | source search engine with minimal configuration. Below, you can state
    | the host and key information for your own Meilisearch installation.
    |
    | See: https://www.meilisearch.com/docs/learn/configuration/instance_options#all-instance-options
    |
    */

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
        'key' => env('MEILISEARCH_KEY'),
        'index-settings' => [
            // 'users' => [
            //     'filterableAttributes'=> ['id', 'name', 'email'],
            // ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Typesense Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Typesense settings. Typesense is an open
    | source search engine using minimal configuration. Below, you will
    | state the host, key, and schema configuration for the instance.
    |
    */

    'typesense' => [
        'client-settings' => [
            'api_key' => env('TYPESENSE_API_KEY', 'xyz'),
            'nodes' => [
                [
                    'host' => env('TYPESENSE_HOST', 'localhost'),
                    'port' => env('TYPESENSE_PORT', '8108'),
                    'path' => env('TYPESENSE_PATH', ''),
                    'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
                ],
            ],
            'nearest_node' => [
                'host' => env('TYPESENSE_HOST', 'localhost'),
                'port' => env('TYPESENSE_PORT', '8108'),
                'path' => env('TYPESENSE_PATH', ''),
                'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
            ],
            'connection_timeout_seconds' => env('TYPESENSE_CONNECTION_TIMEOUT_SECONDS', 2),
            'healthcheck_interval_seconds' => env('TYPESENSE_HEALTHCHECK_INTERVAL_SECONDS', 30),
            'num_retries' => env('TYPESENSE_NUM_RETRIES', 3),
            'retry_interval_seconds' => env('TYPESENSE_RETRY_INTERVAL_SECONDS', 1),
        ],
        // 'max_total_results' => env('TYPESENSE_MAX_TOTAL_RESULTS', 1000),
        'model-settings' => [
            // User::class => [
            //     'collection-schema' => [
            //         'fields' => [
            //             [
            //                 'name' => 'id',
            //                 'type' => 'string',
            //             ],
            //             [
            //                 'name' => 'name',
            //                 'type' => 'string',
            //             ],
            //             [
            //                 'name' => 'created_at',
            //                 'type' => 'int64',
            //             ],
            //         ],
            //         'default_sorting_field' => 'created_at',
            //     ],
            //     'search-parameters' => [
            //         'query_by' => 'name'
            //     ],
            // ],
        ],
        'import_action' => env('TYPESENSE_IMPORT_ACTION', 'upsert'),
    ],

     /*
    |--------------------------------------------------------------------------
    | TNTSearch Configuration
    |--------------------------------------------------------------------------
    */
    'tntsearch' => [
        'storage' => storage_path('search/'),
        'fuzziness' => env('TNTSEARCH_FUZZINESS', false),
        'fuzzy' => [
            'prefix_length' => 2,
            'max_expansions' => 50,
            'distance' => 2,
        ],
        'asYouType' => false,
        'searchBoolean' => env('TNTSEARCH_BOOLEAN', false),
        'maxDocs' => 500,
    ],
];
```

No further changes needed for basic setup.

---

## Step 6 — Create Post Model with Migration

Generate model and migration:

```bash
php artisan make:model Post -m
```

### Update Migration

File:

```
database/migrations/xxxx_create_posts_table.php
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

Run migration:

```bash
php artisan migrate
```

---

## Step 7 — Make Model Searchable

Open:

```
app/Models/Post.php
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use Searchable;

    protected $fillable = [
        'title',
        'body',
    ];

    /**
     * Data that will be indexed by TNTSearch
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,      // VERY IMPORTANT
            'title' => $this->title,
            'body' => $this->body,
        ];
    }
}
```

---

## Step 8 — Create Controller

```bash
php artisan make:controller PostController
```

### Controller Code

```
app/Http/Controllers/PostController.php
```

```php
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
```

---

## Step 9 — Create Blade View

Create folder:

```
resources/views/posts
```

Create file:

```
index.blade.php
```

```blade
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
```

---

## Step 10 — Add Routes

Open:

```
routes/web.php
```

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', [PostController::class, 'index']);
Route::post('/posts', [PostController::class, 'store']);
```

---

## Step 11 — Prepare TNTSearch Storage & Permissions

Before importing records into TNTSearch, you must ensure that the search storage directory exists and has proper write permissions.

### 11.1 Create Search Storage Directory

Navigate to:

```
PHP_Laravel12_TNT_Search/storage
```

Inside the storage folder, ensure there is a folder named:

```
search
```

If it does not exist, create it manually:

```
storage/search/
```

Final path should be:

```
PHP_Laravel12_TNT_Search/storage/search/
```
This folder is required because TNTSearch stores its local SQLite index files here.


### 11.2 Set Folder Write Permissions (Windows - XAMPP)

TNTSearch must be able to write index files into the storage/search folder.

```
PHP_Laravel12_TNT_Search/storage/search/
```

Follow these steps inside this folder:

1. Right-click the storage folder

2. Click Properties

3. Go to the Security tab

4. Click Edit

5. Select each of the following users:

- Users

- SYSTEM

- Administrators

6. Check Full Control

7. Click Apply

8. Click OK


If permissions are not set properly, you may see:

```
SQLSTATE[HY000] [14] unable to open database file
```

### 11.3 Clear Configuration Cache

After creating the folder and setting permissions, clear config cache:

Run:

```bash
php artisan config:clear
php artisan cache:clear
```

This ensures Laravel reads the latest Scout configuration.


## Step 12 — Import Existing Records into Search Index

Very important step.

```bash
php artisan scout:import "App\Models\Post"
```

This builds **local TNTSearch index files**.

---

## Step 13 — Run and Test Project

Start server:

```bash
php artisan serve
```

Open browser:

```
http://127.0.0.1:8000
```

---

## Optional — Rebuild Search Index (If Needed)

If you modify searchable fields or encounter indexing issues, rebuild the index:

### 1. Flush existing index

```bash
php artisan scout:flush "App\Models\Post"
```

### 2.  Re-import records

```bash
php artisan scout:import "App\Models\Post"
```

Use this only when necessary.

---

## Test:

1. Create new posts
2. Search using keywords
3. Results appear instantly

---

## Output

### TNT Search 

<img width="1812" height="1086" alt="Screenshot 2026-02-19 132901" src="https://github.com/user-attachments/assets/9deb1ba1-9b76-4b76-ad02-39575f47d647" />

---

## Project Structure

```
PHP_Laravel12_TNT_Search/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── PostController.php
│   │
│   └── Models/
│       └── Post.php
│
├── config/
│   └── scout.php
│
├── database/
│   └── migrations/
│       └── xxxx_create_posts_table.php
│
├── resources/
│   └── views/
│       └── posts/
│           └── index.blade.php
│
├── routes/
│   └── web.php
│
├── storage/
│   └── search/        ← TNTSearch index files stored here
│
├── .env
├── composer.json
└── README.md
```

---

Your PHP_Laravel12_TNT_Search Project is now ready!
