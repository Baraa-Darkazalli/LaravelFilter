<?php

 
namespace BaraaDark\LaravelFilter\Providers;

use Illuminate\Support\ServiceProvider;
 
final class LaravelFilterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-filter.php', 'laravel-filter');

        // Register bindings or other services

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \BaraaDark\LaravelFilter\Console\Commands\MakeFilterCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/laravel-filter.php' => config_path('laravel-filter.php'),
            ], 'config');
        }
    }

    public function boot()
    {
        // Boot any necessary services or functionality
    }
}