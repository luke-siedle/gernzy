<?php

namespace Lab19\Cart\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Lab19\Cart\Models\Order;
use Lab19\Cart\Models\Payment as PaymentModel;
use Lab19\Cart\Services\PaymentService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Payment
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
    public function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $order = Order::find($args['input']['order_id']);
        $payment = new PaymentModel([
            'provider' => $args['input']['provider']
        ]);
        $payment->order()->associate($order);
        $payment->is_paid = false;
        $payment->save();
        $paymentAction = PaymentService::createAction($payment);
        return $payment;
    }
}
