<?php

namespace Lab19\Cart;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Lab19\Cart\Module\Users\User;

use Lab19\Cart\Module\Users\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
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
        // Auth::extend('cart', function ($app, $name, array $config) {
        //     return new CartGuard(Auth::createUserProvider($config['provider']));
        // });
    }
}
