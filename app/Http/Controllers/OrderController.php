<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function orders()
    {
        $orders = Order::all();
        return response(['orders' => $orders], 200);
    }

    public function getByUser()
    {
        $orders = Order::where('user_id', auth()->user()->id)->get();
        return response(['orders' => $orders], 200);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $cart = new CartService();
            $order = Order::create([
                'user_id' => auth()->user()->id,
                'ammount' => $cart->getTotal(),
                'shipping_address' => $request->shipping_address,
                'order_address' => $request->order_address,
                'order_email' => $request->order_email,
                'order_status' => $request->order_status
            ]);

            foreach ($cart->getItems() as $item) {
                $product = Product::find($item['product_id']);
                $order->order_details()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ]);

                $product->stock = $product->stock - $item['quantity'];
                $product->save();
            }

            Cart::where('user_id', auth()->user()->id)->delete();
            DB::commit();
            
            return response()->json([
                'order' => $order,
                'message' => 'Order created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
