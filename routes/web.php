<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\IssueMemberController;
use App\Http\Controllers\IssueTagController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::resource('issues', IssueController::class);
    Route::resource('tags', TagController::class)->only(['index', 'store']);
    Route::post('issues/{issue}/tags/{tag}', [IssueTagController::class, 'store'])->name('issues.tags.store');
    Route::delete('issues/{issue}/tags/{tag}', [IssueTagController::class, 'destroy'])->name('issues.tags.destroy');
    Route::post('issues/{issue}/members/{user}', [IssueMemberController::class, 'store'])->name('issues.members.store');
    Route::delete('issues/{issue}/members/{user}', [IssueMemberController::class, 'destroy'])->name('issues.members.destroy');
    Route::get('issues/{issue}/comments', [CommentController::class, 'index'])->name('issues.comments.index');
    Route::post('issues/{issue}/comments', [CommentController::class, 'store'])->name('issues.comments.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
