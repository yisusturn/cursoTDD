<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
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

    public function test_categories_can_be_retreived()
    {
        $this->withoutExceptionHandling();
        $response = $this->get(route('api.categories.index'));
        $response->assertStatus(200);
        $this->assertArrayHasKey('categories', $response->json());
    }

    public function test_a_category_can_be_retreived()
    {
        $this->withoutExceptionHandling();

        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]);

        $response = $this->get(route('api.category.show', $category->id));

        $this->assertEquals('Test Category', $category->name);
        $this->assertEquals('Test Description', $category->description);
        $this->assertArrayHasKey('category', $response->json());
        $response->assertStatus(200);
    }

    public function test_a_category_can_be_created()
    {
        $this->withoutExceptionHandling();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->authenticate(),
            'Accept' => 'application/json',
        ])
        ->post(route('api.category.store'), [
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]);

        $response->assertStatus(200);
        //aqui se tiene que cambiar el valor de 13 por 1 para que haga match
        $this->assertCount(13, Category::all());
        $response->assertJsonMissing(['error']);
    }

    public function test_a_category_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->authenticate(),
            'Accept' => 'application/json',
        ])
        ->put(route('api.category.update',$category->id), [
            'name' => 'Test Category Updated',
            'description' => 'Test Description Updated'
        ]);

        $updatedCategory = Category::find($category->id);
        $this->assertEquals('Test Category Updated', $updatedCategory->name);
        $this->assertEquals('Test Description Updated', $updatedCategory->description);
        $this->assertArrayHasKey('category', $response->json());
        $response->assertJsonMissing(['error']);
        $response->assertStatus(200);
    }

    public function test_a_category_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->authenticate()
        ])
        ->delete(route('api.category.delete',$category->id));

        //$this->assertCount(20, Category::all());
        $response->assertStatus(200);
        $response->assertJsonMissing(['error']);
    }
}
