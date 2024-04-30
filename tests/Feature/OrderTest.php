<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    public function authenticate()
    {
        $user = User::create([
            'name' => 'Test User',
            'role' => User::ADMINISTRATOR,
            'email' => time().'@gmail.com',
            'password' => bcrypt('123456')
        
        ]);

        if(!auth()->attempt(['email' => $user->email, 'password' => '123456'])) {
            return response(['message' => 'Login credentials are invalid'], 401);
        }

        return $user->createToken('Auth token')->accessToken;
    }
    
    public function test_orders_can_be_retreived()
    {
        $this->withoutExceptionHandling();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authenticate()
        ])
        ->get(route('api.orders.index'));

        $this->assertArrayHasKey('orders', $response->json());
        $response->assertStatus(200);
    }

    public function test_orders_can_be_retreived_by_authenticated_user()
    {
        $this->withoutExceptionHandling();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authenticate()
        ])
        ->get(route('api.orders.getByUser'));

        $this->assertArrayHasKey('orders', $response->json());
        $response->assertStatus(200);
    }

    public function test_orders_can_be_created()
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticate();

        $category = Category::create([
            'name' => 'Burger',
            'description' => 'Its a good burger'
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'sku' => Str::slug('Test Product'),
            'description' => 'Test Description',
            'price' => 10.2,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('product.jpg')->hashName(),
        ]);

        $product2 = Product::create([
            'name' => 'Test Product 2',
            'sku' => Str::slug('Test Product 2'),
            'description' => 'Test Description 2',
            'price' => 10.2,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('product2.jpg')->hashName(),
        ]);

        Cart::create([
            'user_id' => auth()->user()->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        Cart::create([
            'user_id' => auth()->user()->id,
            'product_id' => $product2->id,
            'quantity' => 1
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])
        ->post(route('api.orders.store'), [
            'shipping_address' => 'Test Address',
            'order_address' => 'Test Order Address',
            'order_email' => 'fake@email.com',
            'order_status' => Order::PENDING
        ]);

        $order = Order::with('order_details')->first();
        $prod1 = Product::find($product->id);
        $prod2 = Product::find($product2->id);

        $this->assertCount(1, Order::all());
        $this->assertEquals(20.4, $order->ammount);
        /*$this->assertEquals('Test Address',$order->shipping_address);
        $this->assertEquals('Test Order Address',$order->order_address);
        $this->assertEquals('fake@email.com',$order->order_email);
        $this->assertEquals(Order::PENDING,$order->order_status);
        $this->assertCount(2, $order->order_details);
        $this->assertEquals(10, $prod1->stock);
        $this->assertEquals(10, $prod2->stock);*/
        $response->assertStatus(200);
    }
}
