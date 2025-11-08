<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\Stop;
use Illuminate\Support\Facades\Hash;

class StudentManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $parentRole = Role::firstOrCreate(['name' => 'parent']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        
        // Create users for each role
        $adminUser = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'mobile' => '1234567890',
        ]);
        
        $teacherUser = User::firstOrCreate([
            'email' => 'teacher@example.com',
        ], [
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password'),
            'role_id' => $teacherRole->id,
            'mobile' => '1234567891',
        ]);
        
        $parentUser = User::firstOrCreate([
            'email' => 'parent@example.com',
        ], [
            'name' => 'Parent User',
            'email' => 'parent@example.com',
            'password' => Hash::make('password'),
            'role_id' => $parentRole->id,
            'mobile' => '1234567892',
        ]);
        
        // Create a user for the student
        $studentUser = User::firstOrCreate([
            'email' => 'student@example.com',
        ], [
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role_id' => $studentRole->id,
            'mobile' => '1234567893',
        ]);
        
        // Create additional student user
        $studentUser2 = User::firstOrCreate([
            'email' => 'student2@example.com',
        ], [
            'name' => 'Second Student',
            'email' => 'student2@example.com',
            'password' => Hash::make('password'),
            'role_id' => $studentRole->id,
            'mobile' => '1234567894',
        ]);
        
        // Create classes
        $class1 = ClassModel::firstOrCreate([
            'class_name' => 'Grade 1-A',
        ], [
            'class_name' => 'Grade 1-A',
            'class_teacher_id' => $teacherUser->id,
            'academic_year' => '2024-2025',
        ]);
        
        $class2 = ClassModel::firstOrCreate([
            'class_name' => 'Grade 2-B',
        ], [
            'class_name' => 'Grade 2-B',
            'class_teacher_id' => $teacherUser->id,
            'academic_year' => '2024-2025',
        ]);
        
        // Create a bus for the route
        $bus = \App\Models\Bus::firstOrCreate([
            'bus_number' => 'BUS001',
        ], [
            'bus_number' => 'BUS001',
            'registration_no' => 'BUS001-REG',
            'model' => 'School Bus Model A',
            'seating_capacity' => 50,
            'status' => true,
            'driver_id' => $teacherUser->id,
            'cleaner_id' => $teacherUser->id,
        ]);
        
        // Create a route for the stops
        $route = \App\Models\Route::firstOrCreate([
            'name' => 'Route 1',
        ], [
            'name' => 'Route 1',
            'bus_id' => $bus->id,
            'total_kilometer' => 10.50,
            'start_time' => '07:00:00',
            'end_time' => '16:00:00',
            'academic_year' => '2024-2025',
        ]);
        
        // Create stops
        $stop1 = Stop::firstOrCreate([
            'name' => 'Main Gate',
        ], [
            'name' => 'Main Gate',
            'route_id' => $route->id,
            'pickup_time' => '08:00:00',
            'drop_time' => '15:00:00',
            'academic_year' => '2024-2025',
            'distance_from_start_km' => 0.00,
        ]);
        
        $stop2 = Stop::firstOrCreate([
            'name' => 'North Gate',
        ], [
            'name' => 'North Gate',
            'route_id' => $route->id,
            'pickup_time' => '08:15:00',
            'drop_time' => '15:15:00',
            'academic_year' => '2024-2025',
            'distance_from_start_km' => 2.50,
        ]);
        
        // Create parent
        $parent = ParentModel::firstOrCreate([
            'user_id' => $parentUser->id,
        ], [
            'user_id' => $parentUser->id,
        ]);
        
        // Create students
        $student1 = Student::firstOrCreate([
            'admission_no' => 'STU001',
        ], [
            'user_id' => $studentUser->id,
            'class_id' => $class1->id,
            'admission_no' => 'STU001',
            'dob' => '2015-06-15',
            'address' => '123 Main St, City, State',
            'pickup_stop_id' => $stop1->id,
            'drop_stop_id' => $stop2->id,
            'academic_year' => '2024-2025',
            'bus_service_active' => true,
        ]);
        
        $student2 = Student::firstOrCreate([
            'admission_no' => 'STU002',
        ], [
            'user_id' => $studentUser2->id,
            'class_id' => $class2->id,
            'admission_no' => 'STU002',
            'dob' => '2014-08-22',
            'address' => '456 Oak Ave, City, State',
            'pickup_stop_id' => $stop2->id,
            'drop_stop_id' => $stop1->id,
            'academic_year' => '2024-2025',
            'bus_service_active' => false,
        ]);
        
        $student3 = Student::firstOrCreate([
            'admission_no' => 'STU003',
        ], [
            'user_id' => $studentUser2->id, // Use existing student user
            'class_id' => $class1->id,
            'admission_no' => 'STU003',
            'dob' => '2015-03-10',
            'address' => '789 Pine Rd, City, State',
            'pickup_stop_id' => $stop1->id,
            'drop_stop_id' => $stop1->id,
            'academic_year' => '2024-2025',
            'bus_service_active' => true,
        ]);
        
        // Link parent to students
        $student1->parents()->attach($parent->id);
        $student3->parents()->attach($parent->id);
        
        $this->command->info('Student management data seeded successfully.');
    }
}