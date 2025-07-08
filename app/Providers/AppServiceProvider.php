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
        if (env('APP_ENV') === 'production') {
            // Configuraciones específicas para producción
           // app()->usePublicPath(base_path() . '/../../public_html/genesis'); 
            app()->usePublicPath(base_path() . '/public');

        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
