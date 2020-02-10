<?php

namespace Gernzy\Server;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use Gernzy\Server\Models\Order;
use Gernzy\Server\Models\Product;
use Gernzy\Server\Models\Tag;
use Gernzy\Server\Models\User;

use Gernzy\Server\Policies\OrderPolicy;
use Gernzy\Server\Policies\ProductPolicy;
use Gernzy\Server\Policies\TagPolicy;
use Gernzy\Server\Policies\UserPolicy;

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
