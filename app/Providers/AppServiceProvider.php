<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        try {
            // Guardar valores de las configuraciones del sitio
            $sql = "SELECT * FROM site_configurations";

            $configurations = DB::select($sql);

            foreach ($configurations as $config) {
                Config::set('site.' . $config->name, $config->value);
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
