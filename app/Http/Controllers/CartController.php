<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        try {
            $carts = Cart::get();

            return response()->json([
                'carts' => $carts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store()
    {
        try {
            $cart = Cart::create([
                'user_id' => auth()->user()->id,
                'product_id' => request('product_id'),
                'quantity' => request('quantity')
            ]);

            return response()->json([
                'cart' => $cart,
                'message' => 'Product added to cart successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Cart $cart)
    {
        try {
            $cart->update($request->all());

            return response()->json([
                'cart' => $cart,
                'message' => 'Cart updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Cart $cart)
    {
        try {
            $cart->delete();

            return response()->json([
                'message' => 'Cart deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
