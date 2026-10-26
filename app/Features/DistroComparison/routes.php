<?php

use App\Features\DistroComparison\Http\Controllers\DistroController;
use Illuminate\Support\Facades\Route;

Route::get('/distro-comparison', function () {
    return view('features.distro-comparison.index');
})->name('distro-comparison.index');

Route::get('/distro/{slug}', [DistroController::class, 'show'])->name('distro.show');
