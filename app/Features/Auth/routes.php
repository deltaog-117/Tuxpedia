<?php

use App\Features\Auth\Actions\LogoutUser;
use App\Features\Auth\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('features.auth.login-wrapper');
})->name('auth.login');

Route::get('/register', function () {
    return view('features.auth.register-wrapper');
})->name('auth.register');

Route::post('/logout', function (LogoutUser $logout) {
    $logout->execute();

    return redirect()->route('auth.login');
})->name('auth.logout');

Route::get('/user/{username}/edit', [ProfileController::class, 'edit'])
    ->where('username', '[a-z0-9_-]+')
    ->middleware('auth')
    ->name('profile.edit');
Route::put('/user/{username}', [ProfileController::class, 'update'])
    ->where('username', '[a-z0-9_-]+')
    ->middleware('auth')
    ->name('profile.update');
