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
use Tymon\JWTAuth\Facades\JWTAuth;

class ParentStudentAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $parentUser;
    protected $parentUser2;
    protected $studentOfParent;
    protected $studentOfOtherParent;
    protected $class;
    protected $parentModel;
    protected $otherParentModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $parentRole = Role::create(['name' => 'parent', 'display_name' => 'Parent']);
        $studentRole = Role::create(['name' => 'student', 'display_name' => 'Student']);
        
        // Create admin user
        $this->adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'name' => 'Admin User',
        ]);
        $this->adminUser->update(['role_id' => $adminRole->id]);

        // Create first parent
        $this->parentUser = User::factory()->create([
            'email' => 'parent1@example.com',
            'password' => bcrypt('password'),
            'name' => 'Parent 1',
        ]);
        $this->parentUser->update(['role_id' => $parentRole->id]);

        // Create second parent
        $this->parentUser2 = User::factory()->create([
            'email' => 'parent2@example.com',
            'password' => bcrypt('password'),
            'name' => 'Parent 2',
        ]);
        $this->parentUser2->update(['role_id' => $parentRole->id]);

        // Create classes
        $this->class = ClassModel::create([
            'class' => 'Class A',
            'academic_year' => '2024-2025',
            'class_teacher_id' => null,
        ]);

        // Create student of first parent
        $studentUser1 = User::factory()->create([
            'email' => 'student1@example.com',
            'password' => bcrypt('password'),
            'name' => 'Student 1',
        ]);
        $studentUser1->update(['role_id' => $studentRole->id]);

        $this->studentOfParent = Student::create([
            'user_id' => $studentUser1->id,
            'class_id' => $this->class->id,
            'admission_no' => 'STU001',
            'dob' => '2010-01-01',
            'address' => '123 Parent St',
            'academic_year' => '2024-2025',
        ]);

        // Create student of second parent
        $studentUser2 = User::factory()->create([
            'email' => 'student2@example.com',
            'password' => bcrypt('password'),
            'name' => 'Student 2',
        ]);
        $studentUser2->update(['role_id' => $studentRole->id]);

        $this->studentOfOtherParent = Student::create([
            'user_id' => $studentUser2->id,
            'class_id' => $this->class->id,
            'admission_no' => 'STU002',
            'dob' => '2010-02-01',
            'address' => '456 Other St',
            'academic_year' => '2024-2025',
        ]);

        // Create parent models
        $this->parentModel = ParentModel::create(['user_id' => $this->parentUser->id]);
        $this->otherParentModel = ParentModel::create(['user_id' => $this->parentUser2->id]);

        // Link students to parents
        StudentParent::create([
            'student_id' => $this->studentOfParent->id,
            'parent_id' => $this->parentModel->id,
        ]);

        StudentParent::create([
            'student_id' => $this->studentOfOtherParent->id,
            'parent_id' => $this->otherParentModel->id,
        ]);
    }

    public function test_parent_can_see_their_own_children_details()
    {
        $token = JWTAuth::fromUser($this->parentUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students');

        $response->assertStatus(200);
        
        // Parse response to check if parent can see their child
        $responseData = $response->json();
        $studentIds = [];
        
        if (isset($responseData['data']['data'])) {
            // Paginated response
            $studentIds = array_column($responseData['data']['data'], 'id');
        } else {
            // Non-paginated response (for parent's students)
            $studentIds = array_column($responseData['data'], 'id');
        }
        
        // Parent should see their own child
        $this->assertContains($this->studentOfParent->id, $studentIds);
    }

    public function test_parent_cannot_see_other_parents_children_details()
    {
        $token = JWTAuth::fromUser($this->parentUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students');

        $response->assertStatus(200);
        
        // Parse response to check if parent can see other parent's child
        $responseData = $response->json();
        $studentIds = [];
        
        if (isset($responseData['data']['data'])) {
            // Paginated response
            $studentIds = array_column($responseData['data']['data'], 'id');
        } else {
            // Non-paginated response (for parent's students)
            $studentIds = array_column($responseData['data'], 'id');
        }
        
        // Parent should NOT see other parent's child
        $this->assertNotContains($this->studentOfOtherParent->id, $studentIds);
    }

    public function test_parent_can_access_specific_child_details()
    {
        $token = JWTAuth::fromUser($this->parentUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students/' . $this->studentOfParent->id);

        // Should be allowed if the student is the parent's child
        $response->assertStatus(200);
    }

    public function test_parent_cannot_access_other_parents_child_details()
    {
        $token = JWTAuth::fromUser($this->parentUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students/' . $this->studentOfOtherParent->id);

        // Should be forbidden if the student is not the parent's child
        $response->assertStatus(403);
    }

    public function test_parent_can_access_my_students_endpoint()
    {
        $token = JWTAuth::fromUser($this->parentUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/parents/me/students');

        $response->assertStatus(200);
        
        // Parse response to check if parent can see their child
        $responseData = $response->json();
        $studentIds = array_column($responseData['data']['data'], 'id');
        
        // Parent should see their child
        $this->assertContains($this->studentOfParent->id, $studentIds);
        // But should not see other parent's child
        $this->assertNotContains($this->studentOfOtherParent->id, $studentIds);
    }
}