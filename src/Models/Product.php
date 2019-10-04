<?php

namespace Lab19\Cart\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class Product extends Model
{

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
     * Featured image relation
     *
     * @var $query
     */
    public function productFeaturedImage()
    {
        return $this->hasMany(ProductAttribute::class)->featuredImage();
    }

    /**
     * Images relation
     *
     * @var $query
     */
    public function images()
    {
        return $this->morphToMany(Image::class, 'cart_image_attachable');
    }

    /**
     * Categories relation
     *
     * @var $query
     */
    public function categories()
    {
        return $this->morphToMany(Category::class, 'cart_categorizable');
    }

    /**
     * Attributes relation
     *
     * @var $query
     */
    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    /**
     * Attributes relation
     *
     * @var $query
     */
    public function prices()
    {
        return $this->hasMany(ProductAttribute::class)->prices();
    }

    /**
     * Attributes relation
     *
     * @var $query
     */
    public function sizes()
    {
        return $this->hasMany(ProductAttribute::class)->sizes();
    }

    /**
     * Meta relation
     *
     * @var $query
     */
    public function productMeta()
    {
        return $this->hasMany(ProductAttribute::class)->meta();
    }

    /**
     * Attributes relation
     *
     * @var $query
     */
    public function productDimensions()
    {
        return $this->hasMany(ProductAttribute::class)->dimensions();
    }

    /**
     * Scope by category(s) ids
     *
     * @var $query
     */
    public function scopeByCategoryIds($query, array $ids)
    {
        $result = $query->whereHas('categories', function (Builder $q) use ($ids) {
            $q->whereIn('category_id', $ids);
        });
        return $result;
    }

    /**
     * Scope by category(s) titles
     *
     * @var $query
     */
    public function scopeByCategoryTitles($query, array $titles)
    {
        return $query->whereHas('categories', function (Builder $q) use ($titles) {
            foreach ($titles as $title) {
                $q->orWhere('title', 'LIKE', '%' . strtolower($title) . '%');
            }
        });
    }

    /**
     * Dimensions attribute
     *
     * @var Array $data
     */
    public function getDimensionsAttribute()
    {
        $dimensions = $this->getAttribute('productDimensions');
        $data = [];
        foreach ($dimensions as $each) {
            $data[ $each->key ] = $each->value;
        }
        return $data;
    }

    /**
     * Attributes relation
     *
     * @var $query
     */
    public function productWeight()
    {
        return $this->hasMany(ProductAttribute::class)->weight();
    }

    /**
     * Images relation
     *
     * @var $query
     */
    public function productImages()
    {
        return $this->hasMany(ProductAttribute::class)->images();
    }

    /**
     * Variants relation
     *
     * @var $query
     */
    public function variants()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    /**
     * Variants relation
     *
     * @var $query
     */
    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    /**
     * Tag relation
     *
     * @var $query
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'cart_taggables')->withTimestamps();
    }

    /**
     * Scope by tag(s) products
     *
     * @var $query
     */
    public function scopeSearchByTag($query, array $tags)
    {
        return $query->whereHas('tags', function ($query) use ($tags) {
            $query->whereIn('tag_id', $tags);
        });
    }

    /**
     * Tag relation
     *
     * @var $query
     */
    public function addTag($tag)
    {
        return $this->tags()->attach($tag);
    }

    /**
     * In stock products scope
     *
     * @var $query
     */
    public function scopeInStock($query)
    {
        return $query->where('status', static::IN_STOCK);
    }

    /**
     * Out of stock products scope
     *
     * @var $query
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('status', static::OUT_OF_STOCK);
    }

    /**
     * Published products
     *
     * @var $query
     */
    public function scopePublished($query)
    {
        return $query->where('published', 1);
    }

    /**
     * Unpublished products
     *
     * @var $query
     */
    public function scopeUnpublished($query)
    {
        return $query->where('published', 0);
    }

    /**
     * Scope by keyword
     *
     * @var $query
     */
    public function scopeSearchByKeyword($query, String $keyword)
    {
        return $query->where('title', 'like', '%' . $keyword . '%');
    }

    /**
     * Scope by attributes
     *
     * @var $query
     */
    public function scopeSearchByAttributes($query, array $attributes)
    {
        $query = $query->whereHas('attributes', function (Builder $q) use ($attributes) {
            foreach ($attributes as $i => $attr) {
                $name = $attr['name'];
                $value = $attr['value'];
                if ($i === 0) {
                    $q->where(function ($qq) use ($name, $value) {
                        $qq->where('key', '=', $name)->where('value', '=', $value);
                    });
                } else {
                    $q->orWhere(function ($qq) use ($name, $value) {
                        $qq->where('key', '=', $name)->where('value', '=', $value);
                    });
                }
            }
        });
        return $query;
    }

    /**
     * FeaturedImage attribute
     *
     * @var Array $data
     */
    public function getFeaturedImageAttribute()
    {
        $attributeArray = $this->getAttribute('productFeaturedImage');
        foreach ($attributeArray as $each) {
            $image = Image::find($each->value);
            return $image;
        }
    }

    /**
     * Meta attribute
     *
     * @var Array $data
     */
    public function getMetaAttribute()
    {
        $meta = $this->getAttribute('productMeta');
        return $meta;
    }

    /**
     * Weight attribute
     *
     * @var Array $data
     */
    public function getWeightAttribute()
    {
        $weight = $this->getAttribute('productWeight');
        $data = [];
        foreach ($weight as $each) {
            $data[$each->key] = $each->value;
        }
        return $data;
    }
}
