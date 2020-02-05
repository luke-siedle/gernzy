<?php
namespace Lab19\Cart\Actions\Helpers;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\Image;
use Lab19\Cart\Models\ProductAttribute;

class Attributes
{

    public function __construct( Product $product = null )
    {
        $this->product = $product;
        $this->attributes = [];
    }

    public function prices( Array $prices ): Attributes
    {
        if( $this->product && count($prices) > 0){
            $this->product->prices()->delete();
        }

        foreach( $prices as $price ){
            $this->attributes[] = [
                'group' => 'prices',
                'key' => $price['currency'],
                'value' => $price['value']
            ];
        }
        return $this;
    }

    public function sizes( Array $sizes ): Attributes
    {
        if( $this->product && count($sizes) > 0 ){
            $this->product->sizes()->delete();
        }

        foreach( $sizes as $size ){
            $this->attributes[] = [
                'group' => 'sizes',
                'key' => 'size',
                'value' => $size['size']
            ];
        }
        return $this;
    }

    public function dimensions( Array $dimensions ): Attributes
    {
        if( $this->product && count($dimensions) > 0 ){
            $this->product->productDimensions()->delete();
        }

        foreach( $dimensions as $type => $value ){
            $this->attributes[] = [
                'group' => 'dimensions',
                'key' => $type,
                'value' => $value
            ];
        }
        return $this;
    }

    public function weight( Array $weight ): Attributes
    {
        if( $this->product && count($weight) > 0 ){
            $this->product->productWeight()->delete();
        }
        foreach( $weight as $type => $value ){
            $this->attributes[] = [
                'group' => 'weight',
                'key' => $type,
                'value' => $value
            ];
        }
        return $this;
    }

    public function meta( Array $meta ): Attributes
    {
        if( $this->product && count($meta) > 0 ){
            $this->product->productMeta()->delete();
        }
        foreach( $meta as $attr ){
            $this->attributes[] = [
                'group' => 'meta',
                'key' => $attr['key'],
                'value' => $attr['value']
            ];
        }
        return $this;
    }

    public function images( Array $images ): Attributes
    {
        if( $this->product && count($images) > 0 ){
            $this->product->productImages()->delete();
        }
        foreach( $images as $id ){
            $this->attributes[] = [
                'group' => 'images',
                'key' => 'image',
                'value' => $id
            ];
        }
        return $this;
    }

    public function featuredImage( $image ): Attributes
    {
        if( $this->product && $image instanceof Image ){
            $this->product->productFeaturedImage()->delete();
        }

        $this->attributes[] = [
            'group' => 'featured_image',
            'key' => 'featured_image',
            'value' => $image->id
        ];

        return $this;
    }

    public function toArray(){
        return $this->attributes;
    }
}
