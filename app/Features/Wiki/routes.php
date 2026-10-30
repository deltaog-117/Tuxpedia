<?php

use App\Features\Wiki\Http\Controllers\CategoryController;
use App\Features\Wiki\Http\Controllers\DiscoveryController;
use App\Features\Wiki\Http\Controllers\PageController;
use App\Features\Wiki\Http\Controllers\TalkController;
use App\Features\Wiki\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/wiki', [PageController::class, 'index'])->name('wiki.index');
Route::get('/random', [DiscoveryController::class, 'random'])->name('wiki.random');
Route::get('/recent-changes', [DiscoveryController::class, 'recent'])->name('wiki.recent');
Route::get('/all-pages', [DiscoveryController::class, 'all'])->name('wiki.all');
Route::get('/wiki/create', function () {
    return view('features.wiki.create', ['prefill' => request()->query('slug')]);
})->middleware('auth')->name('wiki.create');
Route::get('/wiki/{slug}', [PageController::class, 'show'])->name('wiki.show');
Route::get('/wiki/{slug}/edit', function ($slug) {
    return view('features.wiki.edit', ['slug' => $slug]);
})->middleware('auth')->name('wiki.edit');
Route::get('/wiki/{slug}/backlinks', [PageController::class, 'backlinks'])->name('wiki.backlinks');
Route::get('/wiki/{slug}/history', [PageController::class, 'history'])->name('wiki.history');
Route::get('/wiki/{slug}/revision/{sha}', [PageController::class, 'revision'])
    ->where('sha', '[0-9a-f]{40}')
    ->name('wiki.revision');
Route::get('/wiki/{slug}/diff', [PageController::class, 'diff'])->name('wiki.diff');
Route::post('/wiki/{slug}/revert/{sha}', [PageController::class, 'revert'])
    ->where('sha', '[0-9a-f]{40}')
    ->middleware('auth')
    ->name('wiki.revert');
Route::post('/wiki/{slug}/protection', [PageController::class, 'protect'])->middleware('auth')->name('wiki.protect');
Route::get('/wiki/{slug}/talk', [TalkController::class, 'index'])->name('wiki.talk');
Route::get('/wiki/{slug}/talk/{topic}', [TalkController::class, 'show'])
    ->whereNumber('topic')
    ->name('wiki.talk.topic');
Route::post('/wiki/{slug}/talk', [TalkController::class, 'store'])
    ->middleware(['auth', 'throttle:10,1'])
    ->name('wiki.talk.store');
Route::post('/wiki/{slug}/talk/{topic}/replies', [TalkController::class, 'reply'])
    ->whereNumber('topic')
    ->middleware(['auth', 'throttle:10,1'])
    ->name('wiki.talk.reply');
Route::delete('/wiki/{slug}/talk/{topic}', [TalkController::class, 'destroyTopic'])
    ->whereNumber('topic')
    ->middleware('auth')
    ->name('wiki.talk.destroy');
Route::delete('/wiki/{slug}/talk/{topic}/replies/{reply}', [TalkController::class, 'destroyReply'])
    ->whereNumber(['topic', 'reply'])
    ->middleware('auth')
    ->name('wiki.talk.reply.destroy');
Route::get('/categories', [CategoryController::class, 'index'])->name('wiki.categories');
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('wiki.category');
Route::get('/user/{username}', [UserProfileController::class, 'show'])
    ->where('username', '[a-z0-9_-]+')
    ->name('profile.show');
