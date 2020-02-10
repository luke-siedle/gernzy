<?php

namespace Gernzy\Server\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Exceptions\AuthorizationException;
use Illuminate\Support\Str;
use \App;

class Cart
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function addToCart($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $cartService = App::make('Gernzy\ServerService');
        if( $context->request->session ){
            $cart = $cartService->addItemsToCart( $args['input']['items'] );
            return [
                'cart' => $cart
            ];
        }

        return false;
    }

    /**
     * Remove a product item from cart
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function removeFromCart($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $cartService = App::make('Gernzy\ServerService');
        if( $context->request->session ){
            $cart = $cartService->removeItemFromCart($args['input']['product_id']);
            return [
                'cart' => $cart
            ];
        }

        return false;
    }

    /**
     * Update a product item in the cart
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function updateCartQuantity($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $cartService = App::make('Gernzy\ServerService');
        if( $context->request->session ){
            $cart = $cartService->updateCartItemQuantity(
                $args['input']['product_id'],
                $args['input']['quantity']
            );
            return [
                'cart' => $cart
            ];
        }

        return false;
    }
}
