<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class AuthTest extends TestCase
{
    //use RefreshDatabase;

    public function test_login()
    {
        //Artisan::call('passport:install');
        $this->withoutExceptionHandling();

        $user = User::create([
            'name' => 'Test User',
            'email' => time().'@gmail.com',
            'password' => bcrypt('123456')
        ]);

        $user->createToken('Auth token')->accessToken;

        $response = $this->post(route('api.login'),[
            'email' => time().'@gmail.com',
            'password' => '123456'
        ]);

        $response->assertStatus(200);
        $this->assertArrayHasKey('access_token', $response->json());
    }

    public function test_register()
    {
        //Artisan::call('passport:install');
        $this->withoutExceptionHandling();
        $response = $this->post(route('api.register'), [
            'name' => 'Test User',
            'role' => User::ADMINISTRATOR,
            'email' => time().'@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456'
        ]);

        $response->assertStatus(200);
        $this->assertArrayHasKey('access_token', $response->json());
    }
}
