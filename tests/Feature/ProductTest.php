<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
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

    public function test_products_can_be_retreived()
    {
        $this->withoutExceptionHandling();
        $response = $this->get(route('api.products.index'));
        $response->assertStatus(200);
        $this->assertArrayHasKey('products', $response->json());
    }

    public function test_a_product_can_be_retreived()
    {
        $this->withoutExceptionHandling();

        $category = Category::create([
            'name' => 'Burger',
            'description' => 'Its a good burger'
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 10.2,
            'category_id' => $category->id,
            'image' => $file = UploadedFile::fake()->image('product.jpg')->hashName(),
            'stock' => 10,
            'sku' => Str::slug('Test Product'),
        ]);

        $response = $this->get(route('api.product.show', $product->id));

        
        $this->assertArrayHasKey('product', $response->json());
        $response->assertStatus(200);
    }
    
    public function test_a_product_can_be_created()
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticate();

        Storage::fake('products');

        $category = Category::create([
            'name' => 'Burger',
            'description' => 'Its a good burger'
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->post(route('api.products.store'), [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 10.2,
            'category_id' => $category->id,
            'image' => $file = UploadedFile::fake()->image('product.jpg'),
            'stock' => 10
        ]);

        $response->assertStatus(200);
        $response->assertJsonMissing(['Error']);

    }

    public function test_a_product_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 1000
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->authenticate(),
        ])->put(route('api.products.update', $product->id), [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 2000
        ]);

        $response->assertStatus(200);
    }
}
