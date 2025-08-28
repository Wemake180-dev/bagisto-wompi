<?php

namespace Webkul\Wompi\Providers;

use Illuminate\Support\ServiceProvider;

class WompiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');

        $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'wompi');

        $this->publishes([
            __DIR__.'/../Resources/lang' => resource_path('lang/vendor/wompi'),
        ], 'wompi-lang');

        // Load views if they exist
        if (is_dir(__DIR__.'/../Resources/views')) {
            $this->loadViewsFrom(__DIR__.'/../Resources/views', 'wompi');

            $this->publishes([
                __DIR__.'/../Resources/views' => resource_path('views/vendor/wompi'),
            ], 'wompi-views');
        }

        $this->registerConfig();
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();

        // Register the event service provider
        $this->app->register(EventServiceProvider::class);

        // Register the repository
        $this->app->bind(
            \Webkul\Wompi\Contracts\WompiTransaction::class,
            \Webkul\Wompi\Repositories\WompiTransactionRepository::class
        );
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/paymentmethods.php',
            'payment_methods'
        );

        // Merge system configuration for admin panel
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/system.php',
            'core'
        );
    }
}
