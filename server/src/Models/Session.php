<?php

namespace Gernzy\Server\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gernzy_sessions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['data'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['token'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * The cart relationships
     *
     * @var array
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * The user relationship
     *
     * @var array
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
