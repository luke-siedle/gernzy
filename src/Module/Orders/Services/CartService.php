<?php

    namespace Lab19\Cart\Module\Orders\Services;

    use Illuminate\Support\Str;
    use Lab19\Cart\Module\Users\User;
    use Lab19\Cart\Module\Orders\Cart;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Http\Request;
    use App;

    class CartService {

        public function __construct( Request $request ){
            $this->session = $request->session;
        }

        public function getCart(){
            $this->session->load('cart');
            return $this->session->cart;
        }

        public function addItemsToCart( Array $items ){
            $cart = $this->session->cart;
            if( !$cart ){
                $cart = new Cart([
                    'uuid' => $this->session->get('cart_uuid')
                ]);
                $cart->session_id = $this->session->id;
            }

            foreach( $items as $item ){
                $cart->addItem( $item );
            }

            $cart->save();
            $this->session->cart_id = $cart->id;
            $this->session->save();
            return $cart;
        }

        public function removeItemFromCart( Int $productId ){
            $this->session->load('cart');
            $cart = $this->session->cart;
            if( $cart ){
                $removed = $cart->removeItem( $productId );
                if( $removed ){
                    $cart->save();
                    return $cart;
                } else {
                    return false;
                }
            }

            return false;
        }

        public function updateCartItemQuantity( Int $productId, Int $quantity ){
            $this->session->load('cart');
            $cart = $this->session->cart;
            if( $cart ){
                $updated = $cart->updateItemQuantity( $productId, $quantity );
                if( $updated ){
                    $cart->save();
                    return $cart;
                } else {
                    return false;
                }
            }

            return false;
        }

    }
