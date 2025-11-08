<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\Stop;
use App\Models\Bus;
use App\Models\Route;
use App\Models\BusAttendance;
use App\Models\Payment;
use App\Models\Alert;
use App\Models\Announcement;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds for comprehensive dummy data.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $parentRole = Role::firstOrCreate(['name' => 'parent']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $driverRole = Role::firstOrCreate(['name' => 'driver']);
        $cleanerRole = Role::firstOrCreate(['name' => 'cleaner']);
        
        // Create admin user
        $adminUser = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);
        
        // Create teachers
        for ($i = 1; $i <= 3; $i++) {
            User::firstOrCreate([
                'email' => "teacher{$i}@example.com",
            ], [
                'name' => "Teacher {$i}",
                'email' => "teacher{$i}@example.com",
                'password' => Hash::make('password'),
                'role_id' => $teacherRole->id,
            ]);
        }
        
        // Create parents
        for ($i = 1; $i <= 5; $i++) {
            $parentUser = User::firstOrCreate([
                'email' => "parent{$i}@example.com",
            ], [
                'name' => "Parent {$i}",
                'email' => "parent{$i}@example.com",
                'password' => Hash::make('password'),
                'role_id' => $parentRole->id,
            ]);
            
            ParentModel::firstOrCreate([
                'user_id' => $parentUser->id,
            ], [
                'user_id' => $parentUser->id,
            ]);
        }
        
        // Create drivers
        for ($i = 1; $i <= 2; $i++) {
            User::firstOrCreate([
                'email' => "driver{$i}@example.com",
            ], [
                'name' => "Driver {$i}",
                'email' => "driver{$i}@example.com",
                'password' => Hash::make('password'),
                'role_id' => $driverRole->id,
            ]);
        }
        
        // Create cleaners
        for ($i = 1; $i <= 2; $i++) {
            User::firstOrCreate([
                'email' => "cleaner{$i}@example.com",
            ], [
                'name' => "Cleaner {$i}",
                'email' => "cleaner{$i}@example.com",
                'password' => Hash::make('password'),
                'role_id' => $cleanerRole->id,
            ]);
        }
        
        // Create students
        for ($i = 1; $i <= 10; $i++) {
            $studentUser = User::firstOrCreate([
                'email' => "student{$i}@example.com",
            ], [
                'name' => "Student {$i}",
                'email' => "student{$i}@example.com",
                'password' => Hash::make('password'),
                'role_id' => $studentRole->id,
            ]);
        }
        
        // Create classes
        $classNames = ['Grade 1-A', 'Grade 1-B', 'Grade 2-A', 'Grade 2-B', 'Grade 3-A', 'Grade 3-B'];
        $teachers = User::where('role_id', $teacherRole->id)->get();
        
        $classes = [];
        foreach ($classNames as $index => $className) {
            $classes[] = ClassModel::firstOrCreate([
                'class_name' => $className,
            ], [
                'class_name' => $className,
                'academic_year' => '2024-2025',
                'class_teacher_id' => $teachers->random()->id,
            ]);
        }
        
        // Create buses
        $drivers = User::where('role_id', $driverRole->id)->get();
        $cleaners = User::where('role_id', $cleanerRole->id)->get();
        
        $buses = [];
        for ($i = 1; $i <= 5; $i++) {
            $busNumber = sprintf("BUS%02d", $i);
            $regNumber = sprintf("REG%02d", $i);
            $buses[] = Bus::firstOrCreate([
                'bus_number' => $busNumber,
            ], [
                'bus_number' => $busNumber,
                'registration_no' => $regNumber,
                'model' => "School Bus Model {$i}",
                'seating_capacity' => rand(30, 50),
                'status' => true,
                'driver_id' => $drivers->random()->id,
                'cleaner_id' => $cleaners->random()->id,
            ]);
        }
        
        // Create routes
        $routeNames = ['Route A', 'Route B', 'Route C', 'Route D', 'Route E'];
        $routes = [];
        
        foreach ($routeNames as $index => $routeName) {
            $routes[] = Route::firstOrCreate([
                'name' => $routeName,
            ], [
                'name' => $routeName,
                'bus_id' => $buses[$index % count($buses)]->id,
                'total_kilometer' => rand(1000, 2500) / 100, // Convert to decimal
                'start_time' => '07:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'end_time' => '16:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'academic_year' => '2024-2025',
            ]);
        }
        
        // Create stops
        $stopNames = [
            'Main Gate', 'North Gate', 'South Gate', 'East Gate', 'West Gate',
            'Central Park', 'City Center', 'Shopping Mall', 'Railway Station',
            'Bus Stand', 'School Gate', 'Hospital', 'Market', 'Temple'
        ];
        
        foreach ($stopNames as $index => $stopName) {
            Stop::firstOrCreate([
                'name' => $stopName,
            ], [
                'name' => $stopName,
                'route_id' => $routes[$index % count($routes)]->id,
                'pickup_time' => '08:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'drop_time' => '15:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'academic_year' => '2024-2025',
                'distance_from_start_km' => rand(0, 1000) / 100,
            ]);
        }
        
        // Create students with more details
        $students = User::where('role_id', $studentRole->id)->get();
        $allClasses = ClassModel::all();
        $allStops = Stop::all();
        $allParents = ParentModel::all();
        
        $createdStudents = [];
        foreach ($students as $index => $studentUser) {
            $admissionNo = 'STU' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $student = Student::firstOrCreate([
                'admission_no' => $admissionNo,
            ], [
                'user_id' => $studentUser->id,
                'class_id' => $allClasses->random()->id,
                'admission_no' => $admissionNo,
                'dob' => '201' . rand(3, 6) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'address' => $this->generateRandomAddress(),
                'pickup_stop_id' => $allStops->random()->id,
                'drop_stop_id' => $allStops->random()->id,
                'academic_year' => '2024-2025',
                'bus_service_active' => rand(0, 1) ? true : false,
            ]);
            
            // Associate random parents with students
            $numParents = rand(1, 2); // Each student can have 1 or 2 parents
            for ($p = 0; $p < $numParents; $p++) {
                if ($allParents->count() > 0) {
                    $parent = $allParents->random();
                    // Check if the relationship already exists before creating
                    if (!$student->parents()->where('parent_id', $parent->id)->exists()) {
                        $student->parents()->attach($parent->id);
                    }
                }
            }
            
            $createdStudents[] = $student;
        }
        
        // Create attendance records
        $statuses = ['present', 'absent', 'late'];
        for ($i = 0; $i < 50; $i++) {
            $student = $createdStudents[array_rand($createdStudents)];
            $bus = $buses[array_rand($buses)];
            
            $date = now()->subDays(rand(0, 30))->format('Y-m-d');
            
            BusAttendance::firstOrCreate([
                'student_id' => $student->id,
                'bus_id' => $bus->id,
                'date' => $date,
                'academic_year' => '2024-2025',
            ], [
                'student_id' => $student->id,
                'bus_id' => $bus->id,
                'date' => $date,
                'status' => $statuses[array_rand($statuses)],
                'academic_year' => '2024-2025',
                'marked_by' => $adminUser->id,
            ]);
        }
        
        // Create payments
        for ($i = 0; $i < 30; $i++) {
            $student = $createdStudents[array_rand($createdStudents)];
            
            Payment::firstOrCreate([
                'student_id' => $student->id,
                'amount_paid' => rand(1000, 5000),
                'payment_date' => now()->subDays(rand(0, 60))->format('Y-m-d'),
                'payment_type' => ['bus_fee', 'tuition_fee', 'activity_fee', 'annual_fee'][array_rand(['bus_fee', 'tuition_fee', 'activity_fee', 'annual_fee'])],
            ], [
                'student_id' => $student->id,
                'amount_paid' => rand(1000, 5000),
                'total_amount_due' => rand(1000, 5000),
                'payment_date' => now()->subDays(rand(0, 60))->format('Y-m-d'),
                'payment_type' => ['bus_fee', 'tuition_fee', 'activity_fee', 'annual_fee'][array_rand(['bus_fee', 'tuition_fee', 'activity_fee', 'annual_fee'])],
                'status' => ['paid', 'pending', 'overdue'][array_rand(['paid', 'pending', 'overdue'])],
                'academic_year' => '2024-2025',
                'transaction_id' => 'TXN' . strtoupper(substr(md5(uniqid()), 0, 8)),
            ]);
        }
        
        // Create alerts
        $alertTitles = [
            'Bus Delay', 'Route Change', 'School Holiday', 'Emergency Notice',
            'Maintenance Alert', 'Weather Warning', 'Schedule Update'
        ];
        
        for ($i = 0; $i < 15; $i++) {
            Alert::firstOrCreate([
                'description' => $alertTitles[array_rand($alertTitles)] . " " . ($i + 1),
            ], [
                'description' => $alertTitles[array_rand($alertTitles)] . " " . ($i + 1),
                'submitted_by' => $adminUser->id,
                'student_id' => $createdStudents[array_rand($createdStudents)]->id,
                'bus_id' => $buses[array_rand($buses)]->id,
                'route_id' => $routes[array_rand($routes)]->id,
                'severity' => ['low', 'medium', 'high'][array_rand(['low', 'medium', 'high'])],
                'status' => ['new', 'read', 'resolved'][array_rand(['new', 'read', 'resolved'])],
            ]);
        }
        
        // Create announcements
        $announcementTitles = [
            'School Event', 'Policy Update', 'Holiday Notice', 'New Policy',
            'Important Announcement', 'Schedule Change', 'Meeting Notice'
        ];
        
        for ($i = 0; $i < 10; $i++) {
            Announcement::firstOrCreate([
                'title' => $announcementTitles[array_rand($announcementTitles)] . " " . ($i + 1),
            ], [
                'title' => $announcementTitles[array_rand($announcementTitles)] . " " . ($i + 1),
                'description' => $this->generateRandomAnnouncementContent(),
                'created_by' => $adminUser->id,
                'audience' => ['all', 'students', 'parents', 'teachers', 'admin'][array_rand(['all', 'students', 'parents', 'teachers', 'admin'])],
                'is_active' => rand(0, 1) ? true : false,
            ]);
        }
        
        $this->command->info('Comprehensive dummy data seeded successfully.');
    }
    
    private function generateRandomAddress(): string
    {
        $streets = ['Main St', 'Oak Ave', 'Pine Rd', 'Elm St', 'Cedar Ln', 'Maple Dr', 'Washington St', 'Park Ave'];
        $cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia', 'San Antonio'];
        $states = ['NY', 'CA', 'IL', 'TX', 'AZ', 'PA', 'FL', 'WA'];
        
        $street = $streets[array_rand($streets)];
        $city = $cities[array_rand($cities)];
        $state = $states[array_rand($states)];
        $number = rand(100, 9999);
        
        return "{$number} {$street}, {$city}, {$state}";
    }
    
    private function generateRandomAlertDescription(): string
    {
        $descriptions = [
            'The bus will be delayed by 30 minutes due to traffic.',
            'Route will be changed today due to road construction.',
            'School will be closed for the day due to weather conditions.',
            'Emergency notice regarding student safety.',
            'Maintenance work will affect the regular bus schedule.',
            'Weather warning: Extra caution required during pickup/drop.',
            'Schedule has been updated for the next week.'
        ];
        
        return $descriptions[array_rand($descriptions)];
    }
    
    private function generateRandomAnnouncementContent(): string
    {
        $contents = [
            'Please be informed that the school will host a parent-teacher meeting next week.',
            'New safety protocols have been implemented for bus transportation.',
            'Annual school function scheduled for next month. Parents are invited.',
            'Change in bus pickup times effective next Monday.',
            'Important policy update regarding student attendance.',
            'Upcoming holiday schedule. School will be closed for three days.',
            'Meeting with parents scheduled for discussion of academic progress.',
            'New bus routes will be implemented starting next month.'
        ];
        
        return $contents[array_rand($contents)];
    }
}