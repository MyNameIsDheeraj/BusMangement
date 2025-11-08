<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserManagementApiTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $teacherUser;
    protected $parentUser;
    protected $studentUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles first
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $teacherRole = Role::create(['name' => 'teacher', 'display_name' => 'Teacher']);
        $parentRole = Role::create(['name' => 'parent', 'display_name' => 'Parent']);
        $studentRole = Role::create(['name' => 'student', 'display_name' => 'Student']);
        
        // Create test users with their roles
        $this->adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'name' => 'Admin User',
        ]);
        $this->adminUser->update(['role_id' => $adminRole->id]);

        $this->teacherUser = User::factory()->create([
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
            'name' => 'Teacher User',
        ]);
        $this->teacherUser->update(['role_id' => $teacherRole->id]);

        $this->parentUser = User::factory()->create([
            'email' => 'parent@example.com',
            'password' => bcrypt('password'),
            'name' => 'Parent User',
        ]);
        $this->parentUser->update(['role_id' => $parentRole->id]);

        $this->studentUser = User::factory()->create([
            'email' => 'student@example.com',
            'password' => bcrypt('password'),
            'name' => 'Student User',
        ]);
        $this->studentUser->update(['role_id' => $studentRole->id]);
    }

    public function test_post_users_endpoint_with_admin()
    {
        $token = JWTAuth::fromUser($this->adminUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'mobile' => '1234567890',
            'role_id' => $this->adminUser->role_id,
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id',
                     'name',
                     'email',
                     'mobile',
                     'role_id',
                 ]);
    }

    public function test_post_users_endpoint_with_non_admin()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'mobile' => '1234567890',
            'role_id' => $this->adminUser->role_id,
        ]);

        $response->assertStatus(403);
    }

    public function test_get_users_endpoint_with_admin()
    {
        $token = JWTAuth::fromUser($this->adminUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/users');

        $response->assertStatus(200);
    }

    public function test_get_users_endpoint_with_non_admin()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/users');

        $response->assertStatus(403);
    }

    public function test_put_users_endpoint_with_admin()
    {
        $token = JWTAuth::fromUser($this->adminUser);

        $userToUpdate = User::factory()->create([
            'email' => 'update@example.com',
            'password' => bcrypt('password'),
        ]);
        $userToUpdate->update(['role_id' => $this->adminUser->role_id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/v1/users/' . $userToUpdate->id, [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => 'Updated Name',
                     'email' => 'updated@example.com',
                 ]);
    }

    public function test_put_users_endpoint_with_non_admin()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        $userToUpdate = User::factory()->create([
            'email' => 'update@example.com',
            'password' => bcrypt('password'),
        ]);
        $userToUpdate->update(['role_id' => $this->adminUser->role_id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/v1/users/' . $userToUpdate->id, [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertStatus(403);
    }

    public function test_patch_users_endpoint_with_admin()
    {
        $token = JWTAuth::fromUser($this->adminUser);

        $userToUpdate = User::factory()->create([
            'email' => 'patch@example.com',
            'password' => bcrypt('password'),
        ]);
        $userToUpdate->update(['role_id' => $this->adminUser->role_id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson('/api/v1/users/' . $userToUpdate->id, [
            'name' => 'Patched Name',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => 'Patched Name',
                 ]);
    }

    public function test_patch_users_endpoint_with_non_admin()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        $userToUpdate = User::factory()->create([
            'email' => 'patch@example.com',
            'password' => bcrypt('password'),
        ]);
        $userToUpdate->update(['role_id' => $this->adminUser->role_id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson('/api/v1/users/' . $userToUpdate->id, [
            'name' => 'Patched Name',
        ]);

        $response->assertStatus(403);
    }

    public function test_delete_users_endpoint_with_admin()
    {
        $token = JWTAuth::fromUser($this->adminUser);

        $userToDelete = User::factory()->create([
            'email' => 'delete@example.com',
            'password' => bcrypt('password'),
        ]);
        $userToDelete->update(['role_id' => $this->adminUser->role_id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/v1/users/' . $userToDelete->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'User deleted successfully'
                 ]);
    }

    public function test_delete_users_endpoint_with_non_admin()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        $userToDelete = User::factory()->create([
            'email' => 'delete@example.com',
            'password' => bcrypt('password'),
        ]);
        $userToDelete->update(['role_id' => $this->adminUser->role_id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/v1/users/' . $userToDelete->id);

        $response->assertStatus(403);
    }
}