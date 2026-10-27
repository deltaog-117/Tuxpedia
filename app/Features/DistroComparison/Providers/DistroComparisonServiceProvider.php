<?php

declare(strict_types=1);

namespace App\Features\DistroComparison\Providers;

use App\Features\DistroComparison\Http\Livewire\ComparisonTable;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class DistroComparisonServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Livewire::component('comparison-table', ComparisonTable::class);

        Route::group([], base_path('app/Features/DistroComparison/routes.php'));

        $this->loadMigrationsFrom([
            base_path('app/Features/DistroComparison/Database/Migrations'),
        ]);
    }
}
