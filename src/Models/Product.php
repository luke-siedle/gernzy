<?php

    namespace Lab19\Cart\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\HasOne;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;

    use Lab19\Cart\Models\Category;

    class Product extends Model {

        /**
         * Scopes
         */
        const IN_STOCK = 'IN_STOCK';
        const OUT_OF_STOCK = 'OUT_OF_STOCK';


        /**
         * The table associated with the model.
         *
         * @var string
         */
        protected $table = 'cart_products';

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'title',
            'status',
            'published',
            'price_cents',
            'price_currency',
            'short_description',
            'long_description'
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
         * Categories relation
         *
         * @var $query
         */
        public function categories(){
            return $this->morphToMany(Category::class, 'cart_categorizable');
        }

        /**
         * Attributes relation
         *
         * @var $query
         */
        public function attributes(){
            return $this->hasMany(ProductAttribute::class);
        }

        /**
         * Attributes relation
         *
         * @var $query
         */
        public function prices(){
            return $this->hasMany(ProductAttribute::class)->prices();
        }

        /**
         * Attributes relation
         *
         * @var $query
         */
        public function sizes(){
            return $this->hasMany(ProductAttribute::class)->sizes();
        }

        /**
         * Meta relation
         *
         * @var $query
         */
        public function productMeta(){
            return $this->hasMany(ProductAttribute::class)->meta();
        }

        /**
         * Attributes relation
         *
         * @var $query
         */
        public function productDimensions(){
            return $this->hasMany(ProductAttribute::class)->dimensions();
        }

        /**
         * Attributes relation
         *
         * @var $query
         */
        public function productWeight(){
            return $this->hasMany(ProductAttribute::class)->weight();
        }

        /**
         * Variants relation
         *
         * @var $query
         */
        public function variants(){
            return $this->hasMany(Product::class, 'parent_id');
        }

        /**
         * Variants relation
         *
         * @var $query
         */
        public function parent(){
            return $this->belongsTo(Product::class, 'parent_id');
        }

        /**
         * In stock products scope
         *
         * @var $query
         */
        public function scopeInStock( $query ){
            return $query->where('status', static::IN_STOCK );
        }

        /**
         * Out of stock products scope
         *
         * @var $query
         */
        public function scopeOutOfStock( $query ){
            return $query->where('status', static::OUT_OF_STOCK );
        }

        /**
         * Published products
         *
         * @var $query
         */
        public function scopePublished( $query ){
            return $query->where('published', 1 );
        }

        /**
         * Unpublished products
         *
         * @var $query
         */
        public function scopeUnpublished( $query ){
            return $query->where('published', 0 );
        }

        /**
         * Scope by keyword
         *
         * @var $query
         */
        public function scopeSearchByKeyword( $query, String $keyword ){
            return $query->where('title', 'like', '%' . $keyword . '%' );
        }

        /**
         * Dimensions attribute
         *
         * @var Array $data
         */
        public function getDimensionsAttribute(){
            $dimensions = $this->getAttribute('productDimensions');
            $data = [];
            foreach( $dimensions as $each ){
                $data[ $each->key ] = $each->value;
            }
            return $data;
        }

        /**
         * Weight attribute
         *
         * @var Array $data
         */
        public function getWeightAttribute(){
            $weight = $this->getAttribute('productWeight');
            $data = [];
            foreach( $weight as $each ){
                $data[ $each->key ] = $each->value;
            }
            return $data;
        }

        /**
         * Meta attribute
         *
         * @var Array $data
         */
        public function getMetaAttribute(){
            $meta = $this->getAttribute('productMeta');
            return $meta;
        }

    }
