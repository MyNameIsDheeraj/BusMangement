<?php

namespace Tests\Feature\Api\V1;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $teacherUser;
    protected $parentUser;
    protected $studentUser;
    protected $adminToken;
    protected $teacherToken;
    protected $parentToken;
    protected $studentToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Run the seeder to populate roles, permissions, and other required data
        $this->artisan('migrate:fresh --env=testing');
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

        // Create related data first
        $classModel = new \App\Models\ClassModel();
        $classModel->class = 'Test Class';
        $classModel->academic_year = '2025-2026';
        $classModel->class_teacher_id = null; // Will be filled with an actual teacher ID if needed
        $classModel->save();
        
        $stop1 = new \App\Models\Stop();
        $stop1->name = 'Pickup Stop';
        $stop1->location = 'Main Gate';
        $stop1->time = '08:00';
        $stop1->save();
        
        $stop2 = new \App\Models\Stop();
        $stop2->name = 'Drop Stop';
        $stop2->location = 'Main Gate';
        $stop2->time = '15:00';
        $stop2->save();

        // Create users
        $adminRole = Role::where('name', 'admin')->first();
        $teacherRole = Role::where('name', 'teacher')->first();
        $parentRole = Role::where('name', 'parent')->first();
        $studentRole = Role::where('name', 'student')->first();

        $this->adminUser = User::create([
            'role_id' => $adminRole->id,
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password')
        ]);
        
        $this->teacherUser = User::create([
            'role_id' => $teacherRole->id,
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password')
        ]);
        
        $this->parentUser = User::create([
            'role_id' => $parentRole->id,
            'name' => 'Parent User',
            'email' => 'parent@example.com',
            'password' => bcrypt('password')
        ]);
        
        $this->studentUser = User::create([
            'role_id' => $studentRole->id,
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => bcrypt('password')
        ]);

        // Generate tokens
        $this->adminToken = JWTAuth::fromUser($this->adminUser);
        $this->teacherToken = JWTAuth::fromUser($this->teacherUser);
        $this->parentToken = JWTAuth::fromUser($this->parentUser);
        $this->studentToken = JWTAuth::fromUser($this->studentUser);
    }

    public function test_admin_can_get_all_students()
    {
        // Create some students for testing (without related data that might not exist)
        $class = \App\Models\ClassModel::first();
        $stop1 = \App\Models\Stop::first();
        $stop2 = \App\Models\Stop::skip(1)->first();
        
        $student = Student::create([
            'user_id' => $this->studentUser->id,
            'class_id' => $class->id,
            'admission_no' => 'STU001',
            'address' => '123 Test Street',
            'pickup_stop_id' => $stop1->id,
            'drop_stop_id' => $stop2->id,
            'bus_service_active' => true,
            'academic_year' => '2025-2026',
            'dob' => '2010-01-01'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson('/api/v1/students');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => [
                                 'id',
                                 'user',
                                 'class',
                                 'admission_no',
                                 'address',
                                 'pickup_stop',
                                 'drop_stop',
                                 'parents',
                                 'bus_service_active',
                                 'academic_year',
                                 'created_at',
                                 'updated_at',
                             ]
                         ]
                     ],
                     'message',
                     'code',
                     'timestamp'
                 ]);
    }

    public function test_admin_can_create_student()
    {
        $class = \App\Models\ClassModel::first();
        $stop1 = \App\Models\Stop::first();
        $stop2 = \App\Models\Stop::skip(1)->first();
        
        $data = [
            'user_id' => $this->studentUser->id,
            'class_id' => $class->id,
            'admission_no' => 'STU001',
            'address' => '123 Test Street',
            'pickup_stop_id' => $stop1->id,
            'drop_stop_id' => $stop2->id,
            'bus_service_active' => true,
            'academic_year' => '2025-2026',
            'dob' => '2010-01-01'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->postJson('/api/v1/students', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'admission_no' => 'STU001',
                         'address' => '123 Test Street'
                     ]
                 ]);

        $this->assertDatabaseHas('students', [
            'admission_no' => 'STU001',
            'address' => '123 Test Street'
        ]);
    }

    public function test_admin_can_update_student()
    {
        $class = \App\Models\ClassModel::first();
        $stop1 = \App\Models\Stop::first();
        $stop2 = \App\Models\Stop::skip(1)->first();
        
        $student = Student::create([
            'user_id' => $this->studentUser->id,
            'class_id' => $class->id,
            'admission_no' => 'STU002',
            'address' => 'Original Address',
            'pickup_stop_id' => $stop1->id,
            'drop_stop_id' => $stop2->id,
            'bus_service_active' => true,
            'academic_year' => '2025-2026',
            'dob' => '2010-01-01'
        ]);

        $updateData = [
            'address' => 'Updated Address'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->putJson("/api/v1/students/{$student->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'address' => 'Updated Address'
                     ]
                 ]);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'address' => 'Updated Address'
        ]);
    }

    public function test_admin_can_delete_student()
    {
        $class = \App\Models\ClassModel::first();
        $stop1 = \App\Models\Stop::first();
        $stop2 = \App\Models\Stop::skip(1)->first();
        
        $student = Student::create([
            'user_id' => $this->studentUser->id,
            'class_id' => $class->id,
            'admission_no' => 'STU003',
            'address' => 'To be deleted',
            'pickup_stop_id' => $stop1->id,
            'drop_stop_id' => $stop2->id,
            'bus_service_active' => true,
            'academic_year' => '2025-2026',
            'dob' => '2010-01-01'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->deleteJson("/api/v1/students/{$student->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Student deleted successfully'
                 ]);

        $this->assertDatabaseMissing('students', [
            'id' => $student->id
        ]);
    }

    public function test_unauthorized_user_cannot_access_students()
    {
        $response = $this->getJson('/api/v1/students');
        $response->assertStatus(401);
    }
}
