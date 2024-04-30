<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
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

    public function test_a_cart_can_be_retrieved()
    {
        $this->withoutExceptionHandling();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authenticate()
        ])->get(route('api.cart.index'));

        $this->assertArrayHasKey('carts', $response->json());
        $response->assertStatus(200);
    }
    
    public function test_a_cart_can_be_created()
    {
        $this->withoutExceptionHandling();
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

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authenticate()
        ])->post(route('api.cart.store'), [
            'product_id' => $product->id,
            'quantity' => 5
        ]);

        $cart = Cart::first();

        $this->assertCount(1, Cart::all());
        $this->assertEquals($cart->product_id, $product->id);
        $this->assertEquals($cart->quantity, 5);

        $response->assertStatus(200);
    }

    public function test_a_cart_can_be_updated()
    {
        $this->withoutExceptionHandling();

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

        $cart = Cart::create([
            'user_id' => 1,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authenticate()
        ])->put(route('api.cart.update', $cart->id), [
            'product_id' => $product->id,
            'quantity' => 5
        ]);

        $cartUpdated = Cart::find($cart->id);
        $this->assertEquals(5, $cartUpdated->quantity);
        $this->assertArrayHasKey('cart', $response->json());
        $response->assertStatus(200);
    }

    public function test_a_cart_can_be_deleted()
    {
        $this->withoutExceptionHandling();

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

        $cart = Cart::create([
            'user_id' => 1,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authenticate()
        ])->delete(route('api.cart.delete', $cart->id));

        $this->assertCount(0, Cart::all());
        $response->assertStatus(200);
    }
}
