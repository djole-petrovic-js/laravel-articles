<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\CommentsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('posts')->group(function () {
        Route::get('', [PostsController::class, 'index'])->name('post.index');
        Route::get('create', [PostsController::class, 'create'])->name('post.create');
        Route::get('edit/{id}', [PostsController::class, 'edit'])->name('post.edit');
        Route::post('store/{id?}', [PostsController::class, 'store'])->name('post.store');
        Route::delete('destroy/{id?}', [PostsController::class, 'destroy'])->name('post.destroy');
    });

    Route::prefix('news')->group(function () {
        Route::get('', [NewsController::class, 'index'])->name('news.index');
        Route::get('create', [NewsController::class, 'create'])->name('news.create');
        Route::get('edit/{id}', [NewsController::class, 'edit'])->name('news.edit');
        Route::post('store/{id?}', [NewsController::class, 'store'])->name('news.store');
        Route::delete('destroy/{id?}', [NewsController::class, 'destroy'])->name('news.destroy');
    });

    Route::prefix('comments')->group(function () {
        Route::get('', [CommentsController::class, 'index'])->name('comments.index');
        Route::get('view/{id}', [CommentsController::class, 'view'])->name('comments.view');
        Route::get('edit/{id}', [CommentsController::class, 'edit'])->name('comments.edit');
        Route::post('store/{id?}', [CommentsController::class, 'store'])->name('comments.store');
        Route::delete('destroy/{id?}', [CommentsController::class, 'destroy'])->name('comments.destroy');
        Route::post('approve/{id}', [CommentsController::class, 'approve'])->name('comments.approve');
    });
});

require __DIR__.'/auth.php';

Route::get('', [FrontendController::class, 'home'])->name('home');
Route::get('/posts', [FrontendController::class, 'posts'])->name('posts');
Route::get('/posts/{id}', [FrontendController::class, 'postShow'])->name('post.show');
Route::get('/news', [FrontendController::class, 'news'])->name('news');
Route::get('/news/{id}', [FrontendController::class, 'newsShow'])->name('news.show');
Route::post('/addComment',[FrontendController::class, 'addComment'])->name('addComment');
Route::get('/getReplies',[FrontendController::class, 'getReplies'])->name('getReplies');
Route::post('/addReply',[FrontendController::class, 'addReply'])->name('addReply');