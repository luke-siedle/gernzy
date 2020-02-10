<?php

namespace Gernzy\Server\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gernzy_order_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'order_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];
}
