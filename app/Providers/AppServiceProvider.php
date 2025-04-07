<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

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
        // Compartir las categorías con subcategorías para el header
        View::composer('layouts.header', function ($view) {
            $view->with('menuCategories', Category::with('subCategories')->get());
        });
    }
}
