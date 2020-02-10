<?php

    namespace Gernzy\Server\Services;

    use Illuminate\Support\Str;
    use Gernzy\Server\Models\User;
    use Gernzy\Server\Models\Cart;
    use Gernzy\Server\Models\Order;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Http\Request;
    use Gernzy\Server\Services\SessionService;
    use App;

    class OrderService {

        public function __construct( SessionService $sessionService ){
            $this->sessionService = $sessionService;
            $this->session = $sessionService->raw();
        }

        public function myOrders(){
            $user = $this->sessionService->getUser();
            $user->load('orders');
            return $user->orders;
        }

        public function mergePreviousOrdersWithUser( $user ){
            $previousCarts = $this->sessionService->get('previous_carts');
            if( is_array($previousCarts) ){
                foreach( $previousCarts as $previousCartId ){
                    $cart = Cart::find($previousCartId);
                    $order = Order::find($cart->order_id);
                    if( $order ){
                        $order->user_id = $user->id;
                        $order->save();
                    }
                }
            }
        }
    }
