<?php

namespace Lab19\Cart\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFixedPrice extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cart_product_prices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_code',
        'price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * This method converts price in cents to a fixed price
     * For example 32.66 to 32.99
     *
     * @var array
     */
    public function fixPrice()
    {
        if ($price = $this->price) {
            $price = (ceil($price / 100) - 0.01) * 100;
            $this->price = $price;
        }

        return $this;
    }
}
