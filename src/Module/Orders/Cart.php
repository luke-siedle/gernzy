<?php

    namespace Lab19\Cart\Module\Orders;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Lab19\Cart\Module\Orders\Order;
    use Lab19\Cart\Module\Users\Session;

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
            'item_count',
            'items'
        ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            'id',
            'order_id',
            'session_id'
        ];

        /**
         * The attributes that should be cast to native types.
         *
         * @var array
         */
        protected $casts = [
            'items' => 'array'
        ];

        public function order(): BelongsTo
        {
            return $this->belongsTo(Order::class);
        }

        public function session(): BelongsTo
        {
            return $this->belongsTo(Session::class);
        }

        public function addItem( Array $item ){
            $items = $this->getAllItems();
            $items[ $item['product_id'] ] = $item;
            $this->setAttribute('items', $items );
        }

        public function removeItem( Int $productId ){
            $items = $this->getAllItems();
            $item = $items[ $productId ];
            if( $item ){
                unset($items[$item['product_id']]);
                $this->setAttribute('items', $items );
                return true;
            }

            return false;
        }

        public function updateItemQuantity( Int $productId, Int $quantity ){
            $items = $this->getAllItems();
            $item = $items[ $productId ];
            if( $item ){
                $item['quantity'] = $quantity;
                $items[$item['product_id']] = $item;
                $this->setAttribute('items', $items );
                return true;
            }

            return false;
        }

        private function getAllItems(){
            return $this->getAttribute('items') ?? [];
        }
    }
