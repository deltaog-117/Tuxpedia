<?php

declare(strict_types=1);

namespace App\Features\Search\Providers;

use App\Features\Search\Actions\SearchWiki;
use App\Features\Search\Http\Livewire\CommandPalette;
use App\Features\Search\Http\Livewire\SearchBar;
use App\Features\Search\Http\Livewire\SearchResults;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class SearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SearchWiki::class);
    }

    public function boot(): void
    {
        Livewire::component('command-palette', CommandPalette::class);
        Livewire::component('search-bar', SearchBar::class);
        Livewire::component('search-results', SearchResults::class);

        Route::group([], base_path('app/Features/Search/routes.php'));
    }
}
