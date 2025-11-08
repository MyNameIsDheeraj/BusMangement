<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test admin user with role
        $this->adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        // Assuming role_id 1 is for admin - you might need to adjust this based on your role setup
        $this->adminUser->update(['role_id' => 1]);
    }

    public function test_login_endpoint()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                     'expires_in',
                     'user',
                     'role'
                 ]);
    }

    public function test_logout_endpoint()
    {
        // First, get a token
        $token = JWTAuth::fromUser($this->adminUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Successfully logged out'
                 ]);
    }

    public function test_refresh_endpoint()
    {
        // First, get a token
        $token = JWTAuth::fromUser($this->adminUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/refresh');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                     'expires_in',
                     'user'
                 ]);
    }

    public function test_logout_with_invalid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token',
        ])->postJson('/api/v1/logout');

        $response->assertStatus(401);
    }

    public function test_refresh_with_invalid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token',
        ])->postJson('/api/v1/refresh');

        $response->assertStatus(401);
    }

    public function test_logout_unauthenticated()
    {
        $response = $this->postJson('/api/v1/logout');
        $response->assertStatus(401);
    }

    public function test_refresh_unauthenticated()
    {
        $response = $this->postJson('/api/v1/refresh');
        $response->assertStatus(401);
    }
}