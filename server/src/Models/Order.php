<?php

namespace Gernzy\Server\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gernzy_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'telephone',
        'mobile',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_address_postcode',
        'shipping_address_state',
        'shipping_address_country',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_address_postcode',
        'billing_address_state',
        'billing_address_country',
        'payment_method',
        'agree_to_terms',
        'notes',
        'cart_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'cart_id',
        'currency_id',
        'order_status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'int',
        'cart_id' => 'int',
        'currency_id' => 'int'
    ];

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }
}
