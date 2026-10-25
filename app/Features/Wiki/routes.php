<?php

use Illuminate\Support\Facades\Route;
use App\Features\Wiki\Http\Controllers\DiscoveryController;
use App\Features\Wiki\Http\Controllers\PageController;

Route::get('/wiki', [PageController::class, 'index'])->name('wiki.index');
Route::get('/random', [DiscoveryController::class, 'random'])->name('wiki.random');
Route::get('/recent-changes', [DiscoveryController::class, 'recent'])->name('wiki.recent');
Route::get('/all-pages', [DiscoveryController::class, 'all'])->name('wiki.all');
Route::get('/wiki/create', function () {
    return view('features.wiki.create', ['prefill' => request()->query('slug')]);
})->name('wiki.create');
Route::get('/wiki/{slug}', [PageController::class, 'show'])->name('wiki.show');
Route::get('/wiki/{slug}/edit', function ($slug) {
    return view('features.wiki.edit', ['slug' => $slug]);
})->name('wiki.edit');
Route::get('/wiki/{slug}/backlinks', [PageController::class, 'backlinks'])->name('wiki.backlinks');
Route::get('/wiki/{slug}/history', [PageController::class, 'history'])->name('wiki.history');
Route::get('/wiki/{slug}/revision/{sha}', [PageController::class, 'revision'])
    ->where('sha', '[0-9a-f]{40}')
    ->name('wiki.revision');
Route::get('/wiki/{slug}/diff', [PageController::class, 'diff'])->name('wiki.diff');
Route::post('/wiki/{slug}/revert/{sha}', [PageController::class, 'revert'])
    ->where('sha', '[0-9a-f]{40}')
    ->name('wiki.revert');
