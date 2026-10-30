<?php

declare(strict_types=1);

namespace App\Features\Auth\Providers;

use App\Features\Auth\Actions\LoginUser;
use App\Features\Auth\Actions\LogoutUser;
use App\Features\Auth\Actions\RegisterUser;
use App\Features\Auth\Console\PromoteUser;
use App\Features\Auth\Http\Livewire\Login;
use App\Features\Auth\Http\Livewire\Register;
use App\Features\Auth\Models\User;
use App\Features\Auth\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoginUser::class);
        $this->app->bind(RegisterUser::class);
        $this->app->bind(LogoutUser::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([PromoteUser::class]);
        }

        Gate::policy(User::class, UserPolicy::class);

        Livewire::component('auth-login', Login::class);
        Livewire::component('auth-register', Register::class);

        Route::group([], base_path('app/Features/Auth/routes.php'));
    }
}
