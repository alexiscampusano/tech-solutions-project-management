<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Exceptions\Handler;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar nuestro handler de excepciones personalizado
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            Handler::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configurar Carbon para usar español
        Carbon::setLocale(config('app.locale'));
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish');
    }
}
