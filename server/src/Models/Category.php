<?php

    namespace Gernzy\Server\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\MorphedByMany;

    use Gernzy\Server\Models\Product;

    class Category extends Model {

        /**
         * The table associated with the model.
         *
         * @var string
         */
        protected $table = 'cart_categories';

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'title'
        ];

        /**
         * Products that have this category
         */
        public function products()
        {
            return $this->morphedByMany(Product::class, 'cart_categorizable');
        }


    }
