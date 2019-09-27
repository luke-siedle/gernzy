<?php

    namespace Lab19\Cart\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    use Lab19\Cart\Models\Product;

    class ProductAttribute extends Model {

        /**
         * The table associated with the model.
         *
         * @var string
         */
        protected $table = 'cart_product_attributes';

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'group',
            'key',
            'value',
        ];

        /**
         * Product relation
         */
        public function product(){
            return $this->belongsTo(Product::class);
        }

        /**
         * Prices scope
         * $product->prices()
         */
        public function scopePrices( $query )
        {
            return $query->where('group', 'prices');
        }

        /**
         * Images relation
         *
         * @var $query
         */
        public function scopeImages( $query ){
            return $query->where('group', 'images');
        }

        /**
         * Featured image relation
         *
         * @var $query
         */
        public function scopeFeaturedImage( $query ){
            return $query->where('group', 'featured_image');
        }

        /**
         * Custom product meta scope
         * $product->meta()
         */
        public function scopeMeta( $query )
        {
            return $query->where('group', 'meta');
        }

        /**
         * Sizes scope
         * $product->sizes()
         */
        public function scopeSizes( $query )
        {
            return $query->where('group', 'sizes');
        }

        /**
         * Dimensions scope
         * $product->dimensions()
         */
        public function scopeDimensions( $query )
        {
            return $query->where('group', 'dimensions');
        }

        /**
         * Weight scope
         * $product->weight()
         */
        public function scopeWeight( $query )
        {
            return $query->where('group', 'weight');
        }

        /**
         * Get the currency of a price
         * $productAttribute->currency
         */
        public function getCurrencyAttribute()
        {
            return $this->attributes['key'];
        }

        /**
         * Get the value of a price
         * $productAttribute->value
         */
        public function getValueAttribute()
        {
            return $this->attributes['value'];
        }

        /**
         * Get the size
         * $productAttribute->size
         */
        public function getSizeAttribute()
        {
            return $this->attributes['value'];
        }

    }
