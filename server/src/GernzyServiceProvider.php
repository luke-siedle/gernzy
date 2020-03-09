<?php

namespace Gernzy\Server;

use Barryvdh\Cors\ServiceProvider as CorsServiceProvider;
use Gernzy\Server\Models\Cart;
use Gernzy\Server\Observers\CartObserver;
use Gernzy\Server\Services\CartService;
use Gernzy\Server\Services\CurrencyConversionInterface;
use Gernzy\Server\Services\GeolocationInterface;
use Gernzy\Server\Services\GeolocationService;
use Gernzy\Server\Services\MaxmindGeoIP2;
use Gernzy\Server\Services\OpenExchangeRates;
use Gernzy\Server\Services\OrderService;
use Gernzy\Server\Services\SessionService;
use Gernzy\Server\Services\UserService;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\LighthouseServiceProvider;

class GernzyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register dependency packages
        $this->app->register(LighthouseServiceProvider::class);
        $this->app->register(CorsServiceProvider::class);

        // Register core packages
        $this->app->register(AuthServiceProvider::class);

        // Some configuration needs to be overriden by the cart
        // plugin, rather than from within the Laravel app itself
        // This needs to happen before the Lighthouse provider registration
        // or there's a rather cryptic "Server error" returned.
        // This happens because it won't be able to find a "schema.graphql" file.
        $this->app['config']->set('lighthouse.namespaces', []);
        $this->app['config']->set('lighthouse.schema.register', __DIR__ . '/GraphQL/schema/schema.graphql');

        $middleware = $this->app['config']->get('lighthouse.route.middleware') ?? [];

        $this->app['config']->set('lighthouse.route.middleware', array_merge($middleware, [

            // Add CORS dependency package to middleware
            \Barryvdh\Cors\HandleCors::class,

            // Add cart middleware
            \Gernzy\Server\Middleware\CartMiddleware::class,
        ]));


        // Implement our default binding of the geolocation conversion interface
        // GeolocationService
        $this->app->bind(
            GeolocationInterface::class,
            MaxmindGeoIP2::class
        );

        // Implement our default binding of the currency converion interface
        $this->app->bind(
            CurrencyConversionInterface::class,
            OpenExchangeRates::class
        );

        // Bind services
        $this->app->bind('Gernzy\SessionService', SessionService::class);
        $this->app->bind('Gernzy\UserService', UserService::class);
        $this->app->bind('Gernzy\OrderService', OrderService::class);
        $this->app->bind('Gernzy\ServerService', CartService::class);
        $this->app->bind('Gernzy\GeolocationService', GeolocationService::class);

        $this->app->bind('GuzzleHttp\Client', function ($app) {
            return new Client([
                'base_uri' => 'https://openexchangerates.org/api/',
                'timeout' => 2.0,
            ]);
        });

        $directives = [
            'Gernzy\\Server\\GraphQL\\Directives',
        ];

        $this->app['config']->set('lighthouse.namespaces.directives', $directives);

        // Make mail config publishment optional by merging the config from the package.
        $this->mergeConfigFrom(__DIR__ . '/config/mail.php', 'mail');

        // Make cache config publishment optional by merging the config from the package.
        $this->mergeConfigFrom(__DIR__ . '/config/cache.php', 'cache');

        // Make cache config publishment optional by merging the config from the package.
        $this->mergeConfigFrom(__DIR__ . '/config/db.php', 'db');

        // Make cache config publishment optional by merging the config from the package.
        $this->mergeConfigFrom(__DIR__ . '/config/currency.php', 'currency');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Allow developers to override mail config
        $this->publishes([
            __DIR__ . '/config/mail.php' => config_path('mail.php'),
        ]);

        // Allow developers to override cache config
        $this->publishes([
            __DIR__ . '/config/cache.php' => config_path('cache.php'),
        ]);

        // Allow developers to override currency config
        $this->publishes([
            __DIR__ . '/config/currency.php' => config_path('currency.php'),
        ]);

        $this->loadRoutesFrom(__DIR__ . '/Http/routes/web.php');

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'Gernzy\Server');

        // Register observable for the cart model
        Cart::observe(CartObserver::class);
    }
}
