<?php

namespace Lab19\Cart;

use Barryvdh\Cors\ServiceProvider as CorsServiceProvider;
use Illuminate\Support\ServiceProvider;
use Lab19\Cart\Services\CartService;

use Lab19\Cart\Services\OrderService;
use Lab19\Cart\Services\SessionService;
use Lab19\Cart\Services\UserService;
use Nuwave\Lighthouse\LighthouseServiceProvider;

class CartServiceProvider extends ServiceProvider
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
            \Lab19\Cart\Middleware\CartMiddleware::class,
        ]));

        // Bind services
        $this->app->bind('Lab19\SessionService', SessionService::class);
        $this->app->bind('Lab19\UserService', UserService::class);
        $this->app->bind('Lab19\OrderService', OrderService::class);
        $this->app->bind('Lab19\CartService', CartService::class);

        $directives = [
            'Lab19\\Cart\\GraphQL\\Directives',
        ];

        $this->app['config']->set('lighthouse.namespaces.directives', $directives);
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
            __DIR__.'/config/mail.php' => config_path('mail.php'),
        ]);

        $this->loadRoutesFrom(__DIR__.'/Http/routes/web.php');
    }
}
