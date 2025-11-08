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
use App\Models\Payment;
use Tymon\JWTAuth\Facades\JWTAuth;

class ParentPaymentAccessTest extends TestCase
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
    protected $paymentForParentStudent;
    protected $paymentForOtherParentStudent;

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

        // Create payments
        $this->paymentForParentStudent = Payment::create([
            'student_id' => $this->studentOfParent->id,
            'amount_paid' => 500.00,
            'total_amount_due' => 1000.00,
            'payment_type' => 'monthly',
            'status' => 'paid',
            'payment_date' => now(),
            'academic_year' => '2024-2025',
        ]);

        $this->paymentForOtherParentStudent = Payment::create([
            'student_id' => $this->studentOfOtherParent->id,
            'amount_paid' => 300.00,
            'total_amount_due' => 800.00,
            'payment_type' => 'monthly',
            'status' => 'pending',
            'payment_date' => now(),
            'academic_year' => '2024-2025',
        ]);
    }

    public function test_parent_can_see_payments_for_their_own_children()
    {
        $token = JWTAuth::fromUser($this->parentUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/payments');

        $response->assertStatus(200);
        
        // Parse response to check if parent can see payments for their child
        $responseData = $response->json();
        $paymentIds = [];
        
        if (isset($responseData['data']['data'])) {
            // Paginated response
            $paymentIds = array_column($responseData['data']['data'], 'id');
        } else {
            // Non-paginated response (for student)
            $paymentIds = array_column($responseData['data'], 'id');
        }
        
        // Parent should see payments for their own child
        $this->assertContains($this->paymentForParentStudent->id, $paymentIds);
    }

    public function test_parent_cannot_see_payments_for_other_parents_children()
    {
        $token = JWTAuth::fromUser($this->parentUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/payments');

        $response->assertStatus(200);
        
        // Parse response to check if parent can see payments for other parent's child
        $responseData = $response->json();
        $paymentIds = [];
        
        if (isset($responseData['data']['data'])) {
            // Paginated response
            $paymentIds = array_column($responseData['data']['data'], 'id');
        } else {
            // Non-paginated response (for student)
            $paymentIds = array_column($responseData['data'], 'id');
        }
        
        // Parent should NOT see payments for other parent's child
        $this->assertNotContains($this->paymentForOtherParentStudent->id, $paymentIds);
    }

    public function test_parent_can_access_specific_payment_for_their_child()
    {
        $token = JWTAuth::fromUser($this->parentUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/payments/' . $this->paymentForParentStudent->id);

        // Should be allowed if the payment belongs to parent's child
        $response->assertStatus(200);
    }

    public function test_parent_cannot_access_specific_payment_for_other_parent_child()
    {
        $token = JWTAuth::fromUser($this->parentUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/payments/' . $this->paymentForOtherParentStudent->id);

        // Should be forbidden if the payment doesn't belong to parent's child
        $response->assertStatus(403);
    }
}