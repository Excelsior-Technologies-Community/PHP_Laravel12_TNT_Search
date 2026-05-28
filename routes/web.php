<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', [PostController::class, 'index']);
Route::get('/create', [PostController::class, 'create']); 
Route::post('/posts', [PostController::class, 'store']);

Route::get('/posts/search/suggestions', [PostController::class, 'suggestions'])->name('posts.suggestions');

Route::get('/post/{slug}', [PostController::class, 'show']);

Route::post('/status', [PostController::class, 'toggleStatus']);

Route::delete('/delete/{id}', [PostController::class, 'destroy']);
Route::get('/restore/{id}', [PostController::class, 'restore']);
Route::delete('/force-delete/{id}', [PostController::class, 'forceDelete']);