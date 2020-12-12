<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\PostController::class, 'home'])->name('home');
Route::get('/posts/{slug}', [\App\Http\Controllers\PostController::class, 'detail'])->name('posts.detail');
Auth::routes();

Route::post('/comment', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
