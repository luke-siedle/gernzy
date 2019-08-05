<?php

    namespace Lab19\Cart\Module\Orders;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Lab19\Cart\Module\Orders\Order;

    class Cart extends Model
    {

        /**
         * The table associated with the model.
         *
         * @var string
         */
        protected $table = 'cart_carts';

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'item_count'
        ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            'id',
            'order_id'
        ];

        /**
         * The attributes that should be cast to native types.
         *
         * @var array
         */
        protected $casts = [
        ];

        public function order(): BelongsTo
        {
            return $this->belongsTo(Order::class);
        }
    }
