<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Payment;
use Tymon\JWTAuth\Facades\JWTAuth;

class TeacherPaymentAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $teacherUser;
    protected $teacherUser2;
    protected $studentInTeacherClass;
    protected $studentInOtherClass;
    protected $classForTeacher;
    protected $classForOtherTeacher;
    protected $paymentForTeacherStudent;
    protected $paymentForOtherStudent;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $teacherRole = Role::create(['name' => 'teacher', 'display_name' => 'Teacher']);
        $studentRole = Role::create(['name' => 'student', 'display_name' => 'Student']);
        
        // Create admin user
        $this->adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'name' => 'Admin User',
        ]);
        $this->adminUser->update(['role_id' => $adminRole->id]);

        // Create first teacher
        $this->teacherUser = User::factory()->create([
            'email' => 'teacher1@example.com',
            'password' => bcrypt('password'),
            'name' => 'Teacher 1',
        ]);
        $this->teacherUser->update(['role_id' => $teacherRole->id]);

        // Create second teacher
        $this->teacherUser2 = User::factory()->create([
            'email' => 'teacher2@example.com',
            'password' => bcrypt('password'),
            'name' => 'Teacher 2',
        ]);
        $this->teacherUser2->update(['role_id' => $teacherRole->id]);

        // Create classes
        $this->classForTeacher = ClassModel::create([
            'class' => 'Class A',
            'academic_year' => '2024-2025',
            'class_teacher_id' => $this->teacherUser->id,
        ]);

        $this->classForOtherTeacher = ClassModel::create([
            'class' => 'Class B',
            'academic_year' => '2024-2025',
            'class_teacher_id' => $this->teacherUser2->id,
        ]);

        // Create student in teacher's class
        $studentUser1 = User::factory()->create([
            'email' => 'student1@example.com',
            'password' => bcrypt('password'),
            'name' => 'Student 1',
        ]);
        $studentUser1->update(['role_id' => $studentRole->id]);

        $this->studentInTeacherClass = Student::create([
            'user_id' => $studentUser1->id,
            'class_id' => $this->classForTeacher->id,
            'admission_no' => 'STU001',
            'dob' => '2010-01-01',
            'address' => '123 Test St',
            'academic_year' => '2024-2025',
        ]);

        // Create student in other teacher's class
        $studentUser2 = User::factory()->create([
            'email' => 'student2@example.com',
            'password' => bcrypt('password'),
            'name' => 'Student 2',
        ]);
        $studentUser2->update(['role_id' => $studentRole->id]);

        $this->studentInOtherClass = Student::create([
            'user_id' => $studentUser2->id,
            'class_id' => $this->classForOtherTeacher->id,
            'admission_no' => 'STU002',
            'dob' => '2010-02-01',
            'address' => '456 Test St',
            'academic_year' => '2024-2025',
        ]);

        // Create payments
        $this->paymentForTeacherStudent = Payment::create([
            'student_id' => $this->studentInTeacherClass->id,
            'amount_paid' => 500.00,
            'total_amount_due' => 1000.00,
            'payment_type' => 'monthly',
            'status' => 'paid',
            'payment_date' => now(),
            'academic_year' => '2024-2025',
        ]);

        $this->paymentForOtherStudent = Payment::create([
            'student_id' => $this->studentInOtherClass->id,
            'amount_paid' => 300.00,
            'total_amount_due' => 800.00,
            'payment_type' => 'monthly',
            'status' => 'pending',
            'payment_date' => now(),
            'academic_year' => '2024-2025',
        ]);
    }

    public function test_teacher_can_see_payments_of_students_in_their_classes()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/payments');

        $response->assertStatus(200);
        
        // Parse response to check if teacher can see payments for their students
        $responseData = $response->json();
        $paymentIds = [];
        
        // Extract payment IDs from the response
        if (isset($responseData['data']['data'])) {
            // Paginated response
            $paymentIds = array_column($responseData['data']['data'], 'id');
        } else {
            // Non-paginated response (for student)
            $paymentIds = array_column($responseData['data'], 'id');
        }
        
        // Teacher should see payments for students in their class
        $this->assertContains($this->paymentForTeacherStudent->id, $paymentIds);
    }

    public function test_teacher_cannot_see_payments_of_students_in_other_teachers_classes()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/payments');

        $response->assertStatus(200);
        
        // Parse response to check if teacher can see payments for other teachers' students
        $responseData = $response->json();
        $paymentIds = [];
        
        // Extract payment IDs from the response
        if (isset($responseData['data']['data'])) {
            // Paginated response
            $paymentIds = array_column($responseData['data']['data'], 'id');
        } else {
            // Non-paginated response (for student)
            $paymentIds = array_column($responseData['data'], 'id');
        }
        
        // Teacher should NOT see payments for students in other teachers' classes
        $this->assertNotContains($this->paymentForOtherStudent->id, $paymentIds);
    }

    public function test_teacher_can_access_specific_payment_of_their_student()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/payments/' . $this->paymentForTeacherStudent->id);

        // Should be allowed if the payment belongs to a student in teacher's class
        $response->assertStatus(200);
    }

    public function test_teacher_cannot_access_specific_payment_of_other_teachers_student()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/payments/' . $this->paymentForOtherStudent->id);

        // Should be forbidden if the payment doesn't belong to a student in teacher's class
        $response->assertStatus(403);
    }
}