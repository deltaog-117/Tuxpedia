<?php

declare(strict_types=1);

namespace App\Features\FamilyTree\Providers;

use App\Features\FamilyTree\Http\Livewire\FamilyTree;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class FamilyTreeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Livewire::component('family-tree', FamilyTree::class);

        Route::group([], base_path('app/Features/FamilyTree/routes.php'));
    }
}
