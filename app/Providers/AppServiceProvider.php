<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\HttpClientInterface;
use App\Adapters\HttpClientAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registra la interfaz y su adaptador
        $this->app->bind(HttpClientInterface::class, HttpClientAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
