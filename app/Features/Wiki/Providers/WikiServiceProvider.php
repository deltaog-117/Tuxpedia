<?php

declare(strict_types=1);

namespace App\Features\Wiki\Providers;

use App\Features\Wiki\Actions\CreatePage;
use App\Features\Wiki\Actions\DeletePage;
use App\Features\Wiki\Actions\GetPage;
use App\Features\Wiki\Actions\UpdatePage;
use App\Features\Wiki\Console\BackfillRevisions;
use App\Features\Wiki\Console\SyncLinks;
use App\Features\Wiki\Http\Livewire\WikiEditor;
use App\Features\Wiki\Models\Page;
use App\Features\Wiki\Observers\PageObserver;
use App\Features\Wiki\Policies\PagePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class WikiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind actions
        $this->app->bind(CreatePage::class);
        $this->app->bind(UpdatePage::class);
        $this->app->bind(DeletePage::class);
        $this->app->bind(GetPage::class);
    }

    public function boot(): void
    {
        // Register Livewire components
        Livewire::component('wiki-editor', WikiEditor::class);

        if ($this->app->runningInConsole()) {
            $this->commands([BackfillRevisions::class, SyncLinks::class]);
        }

        Page::observe(PageObserver::class);
        Gate::policy(Page::class, PagePolicy::class);

        // Load routes
        Route::group([], base_path('app/Features/Wiki/routes.php'));

        // Load migrations
        $this->loadMigrationsFrom([
            base_path('app/Features/Wiki/Database/Migrations'),
        ]);
    }
}
