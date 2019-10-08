<?php

namespace Lab19\Cart;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use Lab19\Cart\Models\Order;
use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\Tag;
use Lab19\Cart\Models\User;

use Lab19\Cart\Policies\OrderPolicy;
use Lab19\Cart\Policies\ProductPolicy;
use Lab19\Cart\Policies\TagPolicy;
use Lab19\Cart\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Order::class => OrderPolicy::class,
        Product::class => ProductPolicy::class,
        Tag::class => TagPolicy::class
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $guards = $this->app['config']->get('auth.guards');

        $guards['cart'] = [
            'driver' => 'session',
            'provider' => 'cart'
        ];

        $this->app['config']->set('auth.guards', $guards);

        $providers = $this->app['config']->get('auth.providers');

        $providers['cart'] = [
            'driver' => 'eloquent',
            'model' => User::class
        ];

        $this->app['config']->set('auth.providers', $providers);
    }
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('add-to-cart', function ($user) {
            return $user;
        });
    }
}
