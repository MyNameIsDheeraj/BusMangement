<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\ClassModel;
use App\Models\Student;
use Tymon\JWTAuth\Facades\JWTAuth;

class TeacherStudentAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $teacherUser;
    protected $teacherUser2; // A different teacher
    protected $studentInTeacherClass;
    protected $studentInOtherClass;
    protected $classForTeacher;
    protected $classForOtherTeacher;

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
    }

    public function test_teacher_can_see_students_from_assigned_class()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students');

        $response->assertStatus(200);
        
        // Check that the response contains students from the teacher's class
        $response->assertJsonFragment([
            'id' => $this->studentInTeacherClass->id
        ]);
        
        // But does not contain students from other classes (this would be in the paginated results)
        // We need to check specifically that the teacher can't see the other student
        $responseData = $response->json();
        $studentIds = array_column($responseData['data']['data'], 'id');
        
        // Teacher should see their own student
        $this->assertContains($this->studentInTeacherClass->id, $studentIds);
        
        // For a more detailed test, we'll make a specific request for the other class
        $responseForOtherClass = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students/class/' . $this->classForOtherTeacher->id);
        
        // This should return an empty result or a 403 error depending on implementation
        // According to StudentController, teachers can access students by class if they can view that class
        $responseForOtherClass->assertStatus(403); // Should be 403 since they don't teach this class
    }

    public function test_teacher_cannot_access_other_teachers_students_directly()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        // Try to access a student that belongs to another teacher's class
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students/' . $this->studentInOtherClass->id);

        $response->assertStatus(403); // Should be forbidden
    }

    public function test_teacher_can_access_own_class_students()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        // Try to access a student that belongs to their own class
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students/' . $this->studentInTeacherClass->id);

        $response->assertStatus(200); // Should be allowed
    }

    public function test_teacher_can_see_class_students_via_class_endpoint()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students/class/' . $this->classForTeacher->id);

        $response->assertStatus(200);
        
        // Should include the student from this class
        $responseData = $response->json();
        $studentIds = array_column($responseData['data']['data'], 'id');
        $this->assertContains($this->studentInTeacherClass->id, $studentIds);
    }

    public function test_teacher_cannot_see_other_class_students_via_class_endpoint()
    {
        $token = JWTAuth::fromUser($this->teacherUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students/class/' . $this->classForOtherTeacher->id);

        $response->assertStatus(403); // Teacher should not be able to access other teacher's class
    }
}