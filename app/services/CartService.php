<?php

namespace App\Services;

use App\Models\Cart;

class CartService
{
    private $total = 0;

    public function getItems()
    {
        return Cart::where('user_id', auth()->user()->id)->get();
    }

    public function getTotal()
    {
        $this->total = 0;
        $carts = Cart::join('products', 'carts.product_id', '=', 'products.id')
            ->join('users', 'carts.user_id', '=', 'users.id')
            ->select('products.price', 'carts.quantity')
            ->where('user_id', auth()->user()->id)
            ->get();
        
        foreach ($carts as $cart) {
            $this->total += $cart->price * $cart->quantity;
        }

        return $this->total;
    }
}