<?php

namespace Lab19\Cart\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cart_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'session_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'integer'
    ];

    /**
     * Check if user is admin
     *
     * @returns boolean
     */
    public function isAdmin()
    {
        return $this->is_admin === 1;
    }

    /**
     * Check if user is admin
     *
     * @returns boolean
     */
    // public function session(){
    //     return $this->hasOneThrough(Session::class, Cart::class);
    // }

    /**
     * Check if user is admin
     *
     * @returns boolean
     */
    public function cart()
    {
        return $this->hasOneThrough(Cart::class, Session::class);
    }

    /**
     * Check if user is admin
     *
     * @returns boolean
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
