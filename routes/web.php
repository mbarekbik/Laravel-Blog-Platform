<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PostAdminController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostHistoryController;
use App\Http\Controllers\PostSavedController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Main Routes
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/contact', function () {
    return view('contact');
})->name('contact');
Route::get('/post/{id}', [PostController::class, 'show'])->name('post.show');

// Auth Routes
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/postlogin', [AuthController::class, 'login'])->name('postlogin');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ADMIN DASHBOARD
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    // DASHBOARD
    Route::get('', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // POSTS
    Route::resource('posts', PostAdminController::class, ['except' => 'show']);
    Route::get('posts/{id}/show', [PostAdminController::class, 'show'])->name('posts.show');
    Route::get('posts/{id}/auto-save', [PostAdminController::class, 'autoSave'])->name('post.auto-save');
    Route::delete('posts/{id}/reject', [PostAdminController::class, 'reject'])->name('post.reject');
    Route::post('posts/highlight', [PostAdminController::class, 'highlight'])->name('post.highlight');

    // CATEGORIES
    Route::resource('categories', CategoryController::class, ['except' => 'show']);

    // SAVED POSTS
    Route::get('/posts-saved', [PostSavedController::class, 'index'])->name('posts.saved');
    Route::resource('posts-saved', PostSavedController::class, ['except' => ['index', 'create', 'show']]);

    // READ TIME
    Route::post('/calculate-read-time', [PostAdminController::class, 'calculate'])->name('post.readTime');

    // UPLOAD IMAGE (THROUGH THE QUILL) ROUTE
    // Route::post('/image-upload-post', [PostImageController::class, 'store'])->name('image.store');

    // COMMENTS
    Route::resource('comments', CommentController::class, ['except' => 'store']);

    // USERS
    Route::resource('users', UserController::class);

    // ROLES
    Route::resource('roles', RoleController::class);

    // HISTORY POSTS
    Route::get('posts/{id}/edit/history', [PostHistoryController::class, 'index'])->name('history.index');
    Route::get('posts/{id}/edit/history/{history_id}/show', [PostHistoryController::class, 'show'])->name('history.show');
    Route::get('posts/history/{id}/{history_id}', [PostHistoryController::class, 'showJson'])->name('history.showJson');
    Route::get('posts/history/{post}/{history}/revert', [PostHistoryController::class, 'revert'])->name('history.revert');

    // IMAGES
    Route::resource('images', ImageController::class, ['except' => ['create', 'show', 'edit', 'update', 'destroy']]);
    Route::get('images/{directory}/{name}', [ImageController::class, 'show'])->name('images.show');
    Route::delete('images/{directory}/{name}', [ImageController::class, 'destroy'])->name('images.destroy');
});

// Store Comment Route
Route::post('/comment/store', [CommentController::class, 'store'])->name('comments.store');

// Send Mail Route
Route::group(['middleware' => ['auth']], function () {
    Route::get('/send', [MailController::class, 'index'])->name('mail.send');

    // NOTIFICATIONS
    Route::patch('read-notifications', [UserController::class, 'readNotifications'])->name('read.notifications');
    Route::delete('clear-notifications', [UserController::class, 'clearNotifications'])->name('clear.notifications');
});

// Profile Route
Route::get('/profile', function () {
    return view('profile');
})->name('profile');
