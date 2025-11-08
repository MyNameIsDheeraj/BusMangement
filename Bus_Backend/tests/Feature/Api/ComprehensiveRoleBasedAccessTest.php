<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\StudentParent;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Stop;
use App\Models\StudentRoute;
use App\Models\Payment;
use Tymon\JWTAuth\Facades\JWTAuth;

class ComprehensiveRoleBasedAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $teacherUser;
    protected $parentUser;
    protected $driverUser;
    protected $cleanerUser;
    protected $studentUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $teacherRole = Role::create(['name' => 'teacher', 'display_name' => 'Teacher']);
        $parentRole = Role::create(['name' => 'parent', 'display_name' => 'Parent']);
        $driverRole = Role::create(['name' => 'driver', 'display_name' => 'Driver']);
        $cleanerRole = Role::create(['name' => 'cleaner', 'display_name' => 'Cleaner']);
        $studentRole = Role::create(['name' => 'student', 'display_name' => 'Student']);
        
        // Create users with their roles
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

        $this->driverUser = User::factory()->create([
            'email' => 'driver@example.com',
            'password' => bcrypt('password'),
            'name' => 'Driver User',
        ]);
        $this->driverUser->update(['role_id' => $driverRole->id]);

        $this->cleanerUser = User::factory()->create([
            'email' => 'cleaner@example.com',
            'password' => bcrypt('password'),
            'name' => 'Cleaner User',
        ]);
        $this->cleanerUser->update(['role_id' => $cleanerRole->id]);

        $this->studentUser = User::factory()->create([
            'email' => 'student@example.com',
            'password' => bcrypt('password'),
            'name' => 'Student User',
        ]);
        $this->studentUser->update(['role_id' => $studentRole->id]);
    }

    public function test_authentication_endpoints()
    {
        // Test login
        $response = $this->postJson('/api/v1/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'user']);
        
        $token = $response->json('access_token');

        // Test refresh
        $refreshResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/refresh');
        $refreshResponse->assertStatus(200)
                        ->assertJsonStructure(['access_token', 'user']);

        // Test logout
        $logoutResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/logout');
        $logoutResponse->assertStatus(200)
                       ->assertJson(['message' => 'Successfully logged out']);
    }

    public function test_admin_role_access_to_all_endpoints()
    {
        $token = JWTAuth::fromUser($this->adminUser);

        // Test users endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/users');
        $response->assertStatus(200);

        // Test students endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students');
        $response->assertStatus(200);

        // Test buses endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/buses');
        $response->assertStatus(200);

        // Test payments endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/payments');
        $response->assertStatus(200);
    }

    public function test_teacher_role_access()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        // Teachers should not be able to access users endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/users');
        $response->assertStatus(403);

        // Teachers should be able to access students endpoint (but limited to their classes)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students');
        $response->assertStatus(200);

        // Teachers should be able to access buses endpoint (but limited to buses used by their students)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/buses');
        $response->assertStatus(200);

        // Teachers should be able to access payments endpoint (but limited to payments of their students)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/payments');
        $response->assertStatus(200);
    }

    public function test_parent_role_access()
    {
        $token = JWTAuth::fromUser($this->parentUser);

        // Parents should not be able to access users endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/users');
        $response->assertStatus(403);

        // Parents should be able to access students endpoint (but limited to their children)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students');
        $response->assertStatus(200);

        // Parents should be able to access buses endpoint (but limited to buses used by their children)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/buses');
        $response->assertStatus(200);

        // Parents should be able to access payments endpoint (but limited to payments of their children)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/payments');
        $response->assertStatus(200);
    }

    public function test_driver_role_access()
    {
        $token = JWTAuth::fromUser($this->driverUser);

        // Drivers should not be able to access users endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/users');
        $response->assertStatus(403);

        // Drivers should be able to access students endpoint (but limited to students on their bus)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students');
        $response->assertStatus(200);

        // Drivers should be able to access buses endpoint (but limited to their assigned bus)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/buses');
        $response->assertStatus(200);
    }

    public function test_cleaner_role_access()
    {
        $token = JWTAuth::fromUser($this->cleanerUser);

        // Cleaners should not be able to access users endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/users');
        $response->assertStatus(403);

        // Cleaners should be able to access students endpoint (but limited to students on their bus)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students');
        $response->assertStatus(200);

        // Cleaners should be able to access buses endpoint (but limited to their assigned bus)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/buses');
        $response->assertStatus(200);
    }

    public function test_student_role_access()
    {
        $token = JWTAuth::fromUser($this->studentUser);

        // Students should not be able to access users endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/users');
        $response->assertStatus(403);

        // Students should be able to access students endpoint (but limited to themselves)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students');
        $response->assertStatus(200);
    }

    public function test_unauthenticated_access()
    {
        // Test that unauthenticated requests are denied
        $response = $this->getJson('/api/v1/students');
        $response->assertStatus(401);

        $response = $this->getJson('/api/v1/buses');
        $response->assertStatus(401);

        $response = $this->getJson('/api/v1/payments');
        $response->assertStatus(401);

        $response = $this->getJson('/api/v1/users');
        $response->assertStatus(401);
    }
}