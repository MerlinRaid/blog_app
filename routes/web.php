<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentModerationController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Author\AuthorDashboardController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', [BlogController::class, 'index'])->name('home');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

//Salvesta kommentaar sisselogitud kasutajalt
Route::post('/blog/{slug}/comments', [CommentController::class, 'store'])
    ->middleware(['auth',])->name('comments.store');

//Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth','role:Admin|Moderator'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

//Postitused CRUD
    Route::resource('posts', PostController::class) -> except(['show']);
//Kiirtegevused publish ja unpublish
    Route::patch('/posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
    Route::patch('/posts/{post}/unpublish', [PostController::class, 'unpublish'])->name('posts.unpublish');

//Kommentaarid
    Route::get('/comments', [CommentModerationController::class, 'index'])->name('comments.index');
    Route::patch('/comments/{comment}/status', [CommentModerationController::class, 'updateStatus'])->name('comments.updateStatus');
    Route::delete('/comments/{comment}', [CommentModerationController::class, 'destroy'])->name('comments.destroy');

    Route::resource('users', UserController::class) -> except(['show'])-> middleware('role:Admin');

//Kodutöö taasta ja force delete
    Route::patch('comments/{comment}/restore', [CommentModerationController::class, 'restore'])->name('comments.restore')->middleware('role:Admin'); //taastamine ainult Admin
    Route::delete('comments/{comment}/force', [CommentModerationController::class, 'forceDelete'])->name('comments.forceDelete') ->middleware('role:Admin'); //jäädavalt kustutada ainult Admin

    Route::patch('posts/{post}/restore', [PostController::class, 'restore'])->name('posts.restore')->middleware('role:Admin'); //taastamine ainult Admin
    Route::delete('posts/{post}/force', [PostController::class, 'forceDelete'])->name('posts.forceDelete') ->middleware('role:Admin'); //jäädavalt kustutada ainult Admin

//Kategooriad CRUD
    Route::resource('categories', CategoryController::class) -> except(['show']);

//Sildid CRUD
    Route::resource('tags', TagController::class) -> except(['show']);
});

//Author routes
Route::prefix('author')->name('author.')->middleware(['auth','role:Author|Admin'])->group(function () {
    Route::get('/', [AuthorDashboardController::class, 'index'])->name('dashboard');

    Route::resource('posts', \App\Http\Controllers\Author\PostController::class) -> except(['show']);
});