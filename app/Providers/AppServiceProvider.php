<?php

namespace App\Providers;

use App\Repositories\Contracts\ItemRepositoryInterface;
use App\Repositories\ItemRepository;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

/**
 * Application Service Provider
 * 
 * Registers application-level services and bindings.
 * This is where we configure dependency injection for
 * our repository and service layer interfaces.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * 
     * Here we bind interfaces to their concrete implementations,
     * enabling dependency injection and making it easy to swap
     * implementations for testing or future changes.
     */
    public function register(): void
    {
        // Bind Repository Interface to Concrete Implementation
        // This allows us to use dependency injection in our services
        // and easily mock repositories in unit tests
        $this->app->bind(
            ItemRepositoryInterface::class,
            ItemRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
