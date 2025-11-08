<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Stop;
use App\Models\StudentRoute;
use App\Models\StaffProfile;
use Tymon\JWTAuth\Facades\JWTAuth;

class BusStaffStudentAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $driverUser;
    protected $cleanerUser;
    protected $studentOnDriverBus;
    protected $studentOnCleanerBus;
    protected $studentOnOtherBus;
    protected $driverBus;
    protected $cleanerBus;
    protected $otherBus;
    protected $class;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $driverRole = Role::create(['name' => 'driver', 'display_name' => 'Driver']);
        $cleanerRole = Role::create(['name' => 'cleaner', 'display_name' => 'Cleaner']);
        $studentRole = Role::create(['name' => 'student', 'display_name' => 'Student']);
        
        // Create admin user
        $this->adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'name' => 'Admin User',
        ]);
        $this->adminUser->update(['role_id' => $adminRole->id]);

        // Create driver user
        $this->driverUser = User::factory()->create([
            'email' => 'driver@example.com',
            'password' => bcrypt('password'),
            'name' => 'Driver User',
        ]);
        $this->driverUser->update(['role_id' => $driverRole->id]);

        // Create cleaner user
        $this->cleanerUser = User::factory()->create([
            'email' => 'cleaner@example.com',
            'password' => bcrypt('password'),
            'name' => 'Cleaner User',
        ]);
        $this->cleanerUser->update(['role_id' => $cleanerRole->id]);

        // Create classes
        $this->class = ClassModel::create([
            'class' => 'Class A',
            'academic_year' => '2024-2025',
            'class_teacher_id' => null,
        ]);

        // Create buses
        $this->driverBus = Bus::create([
            'name' => 'Driver Bus',
            'bus_number' => 'DB001',
            'capacity' => 50,
            'driver_id' => $this->driverUser->id,
        ]);

        $this->cleanerBus = Bus::create([
            'name' => 'Cleaner Bus',
            'bus_number' => 'CB001',
            'capacity' => 50,
            'cleaner_id' => $this->cleanerUser->id,
        ]);

        $this->otherBus = Bus::create([
            'name' => 'Other Bus',
            'bus_number' => 'OB001',
            'capacity' => 50,
        ]);

        // Create routes
        $driverRoute = Route::create(['name' => 'Driver Route', 'bus_id' => $this->driverBus->id]);
        $cleanerRoute = Route::create(['name' => 'Cleaner Route', 'bus_id' => $this->cleanerBus->id]);
        $otherRoute = Route::create(['name' => 'Other Route', 'bus_id' => $this->otherBus->id]);

        // Create stops
        $driverStop = Stop::create(['name' => 'Driver Stop', 'location' => 'Driver Location', 'route_id' => $driverRoute->id]);
        $cleanerStop = Stop::create(['name' => 'Cleaner Stop', 'location' => 'Cleaner Location', 'route_id' => $cleanerRoute->id]);
        $otherStop = Stop::create(['name' => 'Other Stop', 'location' => 'Other Location', 'route_id' => $otherRoute->id]);

        // Create student who uses the driver's bus
        $studentUser1 = User::factory()->create([
            'email' => 'student1@example.com',
            'password' => bcrypt('password'),
            'name' => 'Student 1',
        ]);
        $studentUser1->update(['role_id' => $studentRole->id]);

        $this->studentOnDriverBus = Student::create([
            'user_id' => $studentUser1->id,
            'class_id' => $this->class->id,
            'admission_no' => 'STU001',
            'dob' => '2010-01-01',
            'address' => '123 Driver St',
            'pickup_stop_id' => $driverStop->id,
            'drop_stop_id' => $driverStop->id,
            'academic_year' => '2024-2025',
        ]);

        // Create student who uses the cleaner's bus
        $studentUser2 = User::factory()->create([
            'email' => 'student2@example.com',
            'password' => bcrypt('password'),
            'name' => 'Student 2',
        ]);
        $studentUser2->update(['role_id' => $studentRole->id]);

        $this->studentOnCleanerBus = Student::create([
            'user_id' => $studentUser2->id,
            'class_id' => $this->class->id,
            'admission_no' => 'STU002',
            'dob' => '2010-02-01',
            'address' => '456 Cleaner St',
            'pickup_stop_id' => $cleanerStop->id,
            'drop_stop_id' => $cleanerStop->id,
            'academic_year' => '2024-2025',
        ]);

        // Create student who uses other bus
        $studentUser3 = User::factory()->create([
            'email' => 'student3@example.com',
            'password' => bcrypt('password'),
            'name' => 'Student 3',
        ]);
        $studentUser3->update(['role_id' => $studentRole->id]);

        $this->studentOnOtherBus = Student::create([
            'user_id' => $studentUser3->id,
            'class_id' => $this->class->id,
            'admission_no' => 'STU003',
            'dob' => '2010-03-01',
            'address' => '789 Other St',
            'pickup_stop_id' => $otherStop->id,
            'drop_stop_id' => $otherStop->id,
            'academic_year' => '2024-2025',
        ]);

        // Create StudentRoute records
        StudentRoute::create(['student_id' => $this->studentOnDriverBus->id, 'stop_id' => $driverStop->id]);
        StudentRoute::create(['student_id' => $this->studentOnCleanerBus->id, 'stop_id' => $cleanerStop->id]);
        StudentRoute::create(['student_id' => $this->studentOnOtherBus->id, 'stop_id' => $otherStop->id]);
    }

    public function test_driver_can_see_students_on_their_bus()
    {
        $token = JWTAuth::fromUser($this->driverUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students');

        $response->assertStatus(200);
        
        // Parse response to check if driver can see students on their bus
        $responseData = $response->json();
        $studentIds = array_column($responseData['data']['data'], 'id');
        
        // Driver should see students on their bus
        $this->assertContains($this->studentOnDriverBus->id, $studentIds);
    }

    public function test_driver_cannot_see_students_on_other_buses()
    {
        $token = JWTAuth::fromUser($this->driverUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students');

        $response->assertStatus(200);
        
        // Parse response to check if driver can see students on other buses
        $responseData = $response->json();
        $studentIds = array_column($responseData['data']['data'], 'id');
        
        // Driver should NOT see students on other buses
        $this->assertNotContains($this->studentOnCleanerBus->id, $studentIds);
        $this->assertNotContains($this->studentOnOtherBus->id, $studentIds);
    }

    public function test_cleaner_can_see_students_on_their_bus()
    {
        $token = JWTAuth::fromUser($this->cleanerUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students');

        $response->assertStatus(200);
        
        // Parse response to check if cleaner can see students on their bus
        $responseData = $response->json();
        $studentIds = array_column($responseData['data']['data'], 'id');
        
        // Cleaner should see students on their bus
        $this->assertContains($this->studentOnCleanerBus->id, $studentIds);
    }

    public function test_cleaner_cannot_see_students_on_other_buses()
    {
        $token = JWTAuth::fromUser($this->cleanerUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students');

        $response->assertStatus(200);
        
        // Parse response to check if cleaner can see students on other buses
        $responseData = $response->json();
        $studentIds = array_column($responseData['data']['data'], 'id');
        
        // Cleaner should NOT see students on other buses
        $this->assertNotContains($this->studentOnDriverBus->id, $studentIds);
        $this->assertNotContains($this->studentOnOtherBus->id, $studentIds);
    }

    public function test_driver_can_access_specific_student_on_their_bus()
    {
        $token = JWTAuth::fromUser($this->driverUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students/' . $this->studentOnDriverBus->id);

        // Should be allowed if the student is on driver's bus
        $response->assertStatus(200);
    }

    public function test_driver_cannot_access_specific_student_not_on_their_bus()
    {
        $token = JWTAuth::fromUser($this->driverUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students/' . $this->studentOnCleanerBus->id);

        // Should be forbidden if the student is not on driver's bus
        $response->assertStatus(403);
    }

    public function test_cleaner_can_access_specific_student_on_their_bus()
    {
        $token = JWTAuth::fromUser($this->cleanerUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students/' . $this->studentOnCleanerBus->id);

        // Should be allowed if the student is on cleaner's bus
        $response->assertStatus(200);
    }

    public function test_cleaner_cannot_access_specific_student_not_on_their_bus()
    {
        $token = JWTAuth::fromUser($this->cleanerUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/students/' . $this->studentOnDriverBus->id);

        // Should be forbidden if the student is not on cleaner's bus
        $response->assertStatus(403);
    }
}