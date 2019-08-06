<?php

namespace Lab19\Cart;

use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\LighthouseServiceProvider;
use Barryvdh\Cors\ServiceProvider as CorsServiceProvider;

use Lab19\Cart\Module\Shop as CoreShop;
use Lab19\Cart\Module\Products as CoreProducts;
use Lab19\Cart\Module\Orders as CoreOrders;
use Lab19\Cart\Module\Users as CoreUsers;

use Lab19\Cart\Module\Users\Services\SessionService;
use Lab19\Cart\Module\Users\Services\UserService;
use Lab19\Cart\AuthServiceProvider;


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
        $this->app->register(CoreShop::class);
        $this->app->register(CoreUsers::class);
        $this->app->register(CoreProducts::class);
        $this->app->register(CoreOrders::class);

        // Some configuration needs to be overriden by the cart
        // plugin, rather than from within the Laravel app itself
        // This needs to happen before the Lighthouse provider registration
        // or there's a rather cryptic "Server error" returned.
        // This happens because it won't be able to find a "schema.graphql" file.
        $this->app['config']->set('lighthouse.namespaces', [] );
        $this->app['config']->set('lighthouse.schema.register', __DIR__ . '/graphql/schema.graphql');

        $middleware = $this->app['config']->get('lighthouse.route.middleware') ?? [];

        // Add CORS dependency package to middleware
        $this->app['config']->set('lighthouse.route.middleware', array_merge(
            $middleware, [
                \Barryvdh\Cors\HandleCors::class,
                \Lab19\Cart\Middleware\CartMiddleware::class
            ]
        ));

        // Bind services
        $this->app->singleton('Lab19\SessionService', SessionService::class );
        $this->app->singleton('Lab19\UserService', UserService::class );

        $mutations = [
            'Lab19\\Cart\\Module\\Users\\GraphQL\\Mutations'
        ];

        // $this->app['config']->set('lighthouse.namespaces.mutations', [] );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
