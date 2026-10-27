<?php

declare(strict_types=1);

namespace App\Features\Terminal\Providers;

use App\Features\Terminal\Http\Livewire\Terminal;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class TerminalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Livewire::component('terminal', Terminal::class);

        Route::group([], base_path('app/Features/Terminal/routes.php'));
    }
}
