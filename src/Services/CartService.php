<?php

namespace Lab19\Cart\Services;

use Illuminate\Http\Request;
use Lab19\Cart\Models\Cart;
use Lab19\Cart\Models\Order;

class CartService
{
    public function __construct(Request $request, SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
        $this->session = $sessionService->raw();
    }

    public function reset()
    {
        if ($this->session) {
            $this->session->load('cart');
            if ($this->session->cart) {
                // Remove association of previous cart
                // from current session
                $previousCartId = $this->session->cart->id;
                $this->session->cart->session_id = null;
                $this->session->cart->save();

                // Create a new cart
                // and associate the session to it
                $cart = new Cart();
                $cart->session_id = $this->session->id;
                $cart->save();

                // Update the session's associated cart
                $this->session->cart_id = $cart->id;

                // Store the previous cart to potentially
                // merge with account creation if required
                $previousCarts = $this->sessionService->get('previous_carts') ?? [];
                $previousCarts[] = $previousCartId;
                $this->sessionService->update([
                    'previous_carts' => $previousCarts
                ]);
            }
        }
    }

    public function setOrder(Order $order)
    {
        $cart = $this->getCart();
        $cart->order_id = $order->id;
        $cart->save();
    }

    public function getCart()
    {
        if ($this->session) {
            $this->session->load('cart');
            if ($this->session->cart) {
                $cart = $this->session->cart;
                return $cart;
            }
        }
        return new Cart();
    }

    public function getItems()
    {
        $cart = $this->getCart();
        return $cart->items;
    }

    public function hasItems()
    {
        $cart = $this->getCart();
        return is_array($cart->items) && count($cart->items) > 0;
    }

    public function addItemsToCart(array $items)
    {
        $cart = $this->session->cart;

        if (!$cart) {
            $cart = new Cart();
            $cart->session_id = $this->session->id;
        }

        foreach ($items as $item) {
            $cart->addItem($item);
        }

        $cart->save();
        $this->session->cart_id = $cart->id;
        $this->session->save();
        return $cart;
    }

    public function removeItemFromCart(Int $productId)
    {
        $this->session->load('cart');
        $cart = $this->session->cart;
        if ($cart) {
            $removed = $cart->removeItem($productId);
            if ($removed) {
                $cart->save();
                return $cart;
            } else {
                return false;
            }
        }

        return false;
    }

    public function updateCartItemQuantity(Int $productId, Int $quantity)
    {
        $this->session->load('cart');
        $cart = $this->session->cart;
        if ($cart) {
            $updated = $cart->updateItemQuantity($productId, $quantity);
            if ($updated) {
                $cart->save();
                return $cart;
            } else {
                return false;
            }
        }

        return false;
    }
}
