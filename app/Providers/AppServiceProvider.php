<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // swap out LengthAwarePaginator class for custom child
        $this->app->bind(
            \Illuminate\Pagination\LengthAwarePaginator::class,
            \App\Services\ParamAwareLengthAwarePaginator::class
        );
    }
}
