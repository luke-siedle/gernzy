<?php

    namespace Lab19\Cart\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\HasOne;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\HasManyThrough;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;

    use Lab19\Cart\Models\Product;

    class Tag extends Model {


        /**
         * The table associated with the model.
         *
         * @var string
         */
        protected $table = 'cart_tags';

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'tag'
        ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
        ];

        /**
         * The attributes that should be cast to native types.
         *
         * @var array
         */
        protected $casts = [
        ];


        /**
         * Product relation
         *
         * @var $query
         */
        public function products(){
            return $this->morphedByMany(Product::class, 'taggable','cart_taggables')->withTimestamps();
        }

    }
