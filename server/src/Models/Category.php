<?php

    namespace Gernzy\Server\Models;

    use Illuminate\Database\Eloquent\Model;


    class Category extends Model
    {

        /**
         * The table associated with the model.
         *
         * @var string
         */
        protected $table = 'gernzy_categories';

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
            return $this->morphedByMany(Product::class, 'gernzy_categorizable');
        }
    }
