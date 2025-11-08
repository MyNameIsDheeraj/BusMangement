<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles as specified in the requirements
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $parentRole = Role::firstOrCreate(['name' => 'parent']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $driverRole = Role::firstOrCreate(['name' => 'driver']);
        $cleanerRole = Role::firstOrCreate(['name' => 'cleaner']);
        
        // Create permissions as specified in the requirements
        $permissions = [
            'view_student',
            'create_student', 
            'edit_student',
            'delete_student',
            'view_payment',
            'create_payment',
            'edit_payment',
            'delete_payment',
            'view_bus_route',
            'create_bus_route',
            'edit_bus_route',
            'delete_bus_route',
            'view_attendance',
            'mark_attendance',
            'edit_attendance',
            'delete_attendance',
            'view_alert',
            'create_alert',
            'edit_alert',
            'delete_alert',
            'view_announcement',
            'create_announcement',
            'edit_announcement',
            'delete_announcement',
            'manage_settings',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_staff',
            'create_staff',
            'edit_staff',
            'delete_staff'
        ];
        
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }
        
        // Define permissions for each role based on requirements
        $this->assignPermissionsToRole($adminRole, $permissions); // Admin has all permissions
        
        // Teacher permissions
        $teacherPermissions = [
            'view_student',      // Can view students in their class
            'view_payment',      // Can view payments for class students
            'view_bus_route',    // Can view bus info for class students
            'view_attendance',   // Can view attendance for class students
            'mark_attendance',   // Can mark attendance
            'view_alert',        // Can view alerts for class students
            'create_alert',      // Can create alerts
            'view_announcement', // Can view announcements
        ];
        $this->assignPermissionsToRole($teacherRole, $teacherPermissions);
        
        // Parent permissions
        $parentPermissions = [
            'view_student',      // Can view their children
            'view_payment',      // Can view their children's payments
            'view_bus_route',    // Can view their children's bus routes
            'view_attendance',   // Can view their children's attendance
            'create_payment',    // Can make payments for their children
            'view_alert',        // Can view their children's alerts
            'view_announcement', // Can view announcements
        ];
        $this->assignPermissionsToRole($parentRole, $parentPermissions);
        
        // Student permissions
        $studentPermissions = [
            'view_student',      // Can view their own details
            'view_payment',      // Can view their own payments
            'view_bus_route',    // Can view their own bus route
            'view_attendance',   // Can view their own attendance
            'view_announcement', // Can view announcements
        ];
        $this->assignPermissionsToRole($studentRole, $studentPermissions);
        
        // Driver permissions
        $driverPermissions = [
            'view_bus_route',    // Can view their assigned route
            'view_student',      // Can view students on their route
            'view_staff',        // Can view staff related to their bus
            'mark_attendance',   // Can mark attendance for students on their route
            'create_alert',      // Can create alerts
            'view_alert',        // Can view alerts
            'view_announcement', // Can view announcements
        ];
        $this->assignPermissionsToRole($driverRole, $driverPermissions);
        
        // Cleaner permissions
        $cleanerPermissions = [
            'view_bus_route',    // Can view their assigned route
            'view_student',      // Can view students on their route
            'view_staff',        // Can view staff related to their bus
            'mark_attendance',   // Can mark attendance for students on their route
            'create_alert',      // Can create alerts
            'view_alert',        // Can view alerts
            'view_announcement', // Can view announcements
        ];
        $this->assignPermissionsToRole($cleanerRole, $cleanerPermissions);
        
        // Create initial settings as specified in the requirements
        $this->createInitialSettings();
    }
    
    /**
     * Assign permissions to a role
     */
    private function assignPermissionsToRole($role, $permissionNames)
    {
        $permissions = Permission::whereIn('name', $permissionNames)->get();
        $role->permissions()->sync($permissions->pluck('id')->toArray());
    }
    
    /**
     * Create initial settings as specified in the requirements
     */
    private function createInitialSettings()
    {
        $settings = [
            [
                'key' => 'current_academic_year',
                'value' => '2025-2026',
                'display_name' => 'Current Academic Year',
                'data_type' => 'string',
                'is_system_locked' => 1,
                'is_visible' => 1
            ],
            [
                'key' => 'bus_service_enabled',
                'value' => '1',
                'display_name' => 'Bus Service Enabled',
                'data_type' => 'boolean',
                'is_system_locked' => 0,
                'is_visible' => 1
            ],
            [
                'key' => 'payment_due_date',
                'value' => '5',
                'display_name' => 'Payment Due Date',
                'data_type' => 'integer',
                'is_system_locked' => 0,
                'is_visible' => 1
            ],
            [
                'key' => 'late_fee_percentage',
                'value' => '5.00',
                'display_name' => 'Late Fee (%)',
                'data_type' => 'decimal',
                'is_system_locked' => 0,
                'is_visible' => 1
            ],
            [
                'key' => 'admin_email',
                'value' => 'admin@school.edu',
                'display_name' => 'Admin Email',
                'data_type' => 'string',
                'is_system_locked' => 0,
                'is_visible' => 1
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Kolkata',
                'display_name' => 'System Timezone',
                'data_type' => 'string',
                'is_system_locked' => 1,
                'is_visible' => 1
            ]
        ];
        
        foreach ($settings as $settingData) {
            \App\Models\Setting::firstOrCreate(
                ['key' => $settingData['key']], 
                $settingData
            );
        }
    }
}
