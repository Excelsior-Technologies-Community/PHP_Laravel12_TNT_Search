<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', [PostController::class, 'index']);
Route::post('/posts', [PostController::class, 'store']);

// Route::get('/', function () {
//     return view('welcome');
// });
