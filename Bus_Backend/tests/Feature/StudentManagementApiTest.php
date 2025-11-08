<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Stop;

class StudentManagementApiTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $teacherUser;
    protected $parentUser;
    protected $studentUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users with their roles
        $this->adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->adminUser->update(['role_id' => 1]); // Assuming admin role id is 1

        $this->teacherUser = User::factory()->create([
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->teacherUser->update(['role_id' => 2]); // Assuming teacher role id is 2

        $this->parentUser = User::factory()->create([
            'email' => 'parent@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->parentUser->update(['role_id' => 3]); // Assuming parent role id is 3

        $this->studentUser = User::factory()->create([
            'email' => 'student@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->studentUser->update(['role_id' => 4]); // Assuming student role id is 4
    }

    public function test_student_index_endpoint_with_admin()
    {
        $token = $this->adminUser->createToken('test')->plainTextToken;
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/students');

        $response->assertStatus(200);
    }

    public function test_student_index_endpoint_with_teacher()
    {
        $token = $this->teacherUser->createToken('test')->plainTextToken;
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/students');

        // Teacher should have access but limited to their classes
        $response->assertStatus(200);
    }

    public function test_student_login_and_get_all_students()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                     'expires_in',
                     'user'
                 ]);

        $token = $response->json('access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/students');

        $response->assertStatus(200);
    }

    public function test_student_creation_with_admin()
    {
        $class = ClassModel::factory()->create();
        $stop = Stop::factory()->create();
        
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('access_token');
        
        $userData = User::factory()->create([
            'role_id' => 4, // Student role
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/students', [
            'user_id' => $userData->id,
            'class_id' => $class->id,
            'admission_no' => 'TEST001',
            'dob' => '2010-01-01',
            'address' => '123 Test St',
            'academic_year' => '2024-2025',
        ]);

        $response->assertStatus(201);
    }
}