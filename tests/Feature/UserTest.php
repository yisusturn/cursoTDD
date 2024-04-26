<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    //use RefreshDatabase;

    public function authenticate()
    {
        //Artisan::call('passport:install');

        $user = User::create([
            'name' => 'Test User',
            'role' => User::CLIENT,
            'email' => time().'@gmail.com',
            'password' => bcrypt('123456')
        
        ]);

        if(!auth()->attempt(['email' => $user->email, 'password' => '123456'])) {
            return response(['message' => 'Login credentials are invalid'], 401);
        }

        return $user->createToken('Auth token')->accessToken;
    }

    public function test_a_user_can_be_retrieved()
    {
        $this->withoutExceptionHandling();
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->get(route('api.auth.me'));

        $response->assertStatus(200);
        $this->assertArrayHasKey('user', $response->json());
    }

    public function test_logout()
    {
        $this->withoutExceptionHandling();
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->get(route('api.auth.logout'));

        $response->assertStatus(200);
        $this->assertArrayHasKey('message', $response->json());
        $this->assertEquals('Logged out successfully', $response->json()['message']);
    }
}
