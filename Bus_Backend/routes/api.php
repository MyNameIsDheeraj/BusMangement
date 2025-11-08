<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\StudentParentController;
use App\Http\Controllers\Api\BusController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\StopController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\ParentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API v1 routes
Route::prefix('v1')->group(function () {

    // Test route - public
    Route::get('test', [TestController::class, 'index']);

    // Public routes
    Route::post('login', [AuthController::class, 'login']);

    // Refresh token route - should work even with expired tokens (within refresh_ttl)
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('throttle:api-authenticated');

    // Protected routes - require authentication and rate limiting
    Route::middleware(['auth:api', 'throttle:api-authenticated'])->group(function () {
    // Auth routes
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    
    // Registration route - admin only
    Route::post('register', [AuthController::class, 'register'])->middleware('role:admin');
    
    // User routes - admin only
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });
    
    // Student routes with proper role-based access
    Route::prefix('students')->middleware('role:admin,teacher,parent,student')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->middleware('permission:view_student');
        Route::post('/', [StudentController::class, 'store'])->middleware('role:admin')->middleware('permission:create_student');
        Route::get('/{id}', [StudentController::class, 'show'])->middleware('permission:view_student');
        Route::put('/{id}', [StudentController::class, 'update'])->middleware('permission:edit_student');
        Route::patch('/{id}', [StudentController::class, 'update'])->middleware('permission:edit_student');
        Route::delete('/{id}', [StudentController::class, 'destroy'])->middleware('role:admin')->middleware('permission:delete_student');
        
        // Additional student management routes
        Route::post('/bulk-delete', [StudentController::class, 'bulkDelete'])->middleware('role:admin')->middleware('permission:delete_student');
        Route::post('/{studentId}/assign-parent', [StudentController::class, 'assignParent'])->middleware('role:admin')->middleware('permission:edit_student');
        Route::delete('/{studentId}/remove-parent/{parentId}', [StudentController::class, 'removeParent'])->middleware('role:admin')->middleware('permission:edit_student');
        Route::get('/class/{classId}', [StudentController::class, 'studentsByClass'])->middleware('permission:view_student');
    });
    
    // Student-Parent relationship routes
    Route::prefix('student-parents')->middleware('role:admin,teacher,parent,student')->group(function () {
        Route::get('/', [StudentParentController::class, 'index'])->middleware('permission:view_student');
        Route::post('/', [StudentParentController::class, 'store'])->middleware('role:admin')->middleware('permission:edit_student');
        Route::get('/{id}', [StudentParentController::class, 'show'])->middleware('permission:view_student');
        Route::delete('/{id}', [StudentParentController::class, 'destroy'])->middleware('role:admin')->middleware('permission:delete_student');
        Route::get('/student/{studentId}', [StudentParentController::class, 'getParentsForStudent'])->middleware('permission:view_student');
        Route::get('/parent/{parentId}', [StudentParentController::class, 'getStudentsForParent'])->middleware('permission:view_student');
    });
    
    // Bus routes
    Route::middleware('role:admin,teacher,parent,student,driver,cleaner')->group(function () {
        Route::get('buses', [BusController::class, 'index']);
        Route::get('buses/{id}', [BusController::class, 'show']);
        Route::middleware('role:admin')->group(function () {
            Route::post('buses', [BusController::class, 'store']);
            Route::put('buses/{id}', [BusController::class, 'update']);
            Route::patch('buses/{id}', [BusController::class, 'update']);
            Route::delete('buses/{id}', [BusController::class, 'destroy']);
        });
    });
    
    // Route routes
    Route::middleware('role:admin,teacher,parent,student,driver,cleaner')->group(function () {
        Route::get('routes', [RouteController::class, 'index']);
        Route::get('routes/{id}', [RouteController::class, 'show']);
        Route::middleware('role:admin')->group(function () {
            Route::post('routes', [RouteController::class, 'store']);
            Route::put('routes/{id}', [RouteController::class, 'update']);
            Route::patch('routes/{id}', [RouteController::class, 'update']);
            Route::delete('routes/{id}', [RouteController::class, 'destroy']);
        });
    });
    
    // Stop routes
    Route::middleware('role:admin,teacher,parent,student,driver,cleaner')->group(function () {
        Route::get('stops', [StopController::class, 'index']);
        Route::get('stops/{id}', [StopController::class, 'show']);
        Route::middleware('role:admin')->group(function () {
            Route::post('stops', [StopController::class, 'store']);
            Route::put('stops/{id}', [StopController::class, 'update']);
            Route::patch('stops/{id}', [StopController::class, 'update']);
            Route::delete('stops/{id}', [StopController::class, 'destroy']);
        });
    });
    
    // Payment routes
    Route::middleware('role:admin,teacher,parent,student')->group(function () {
        Route::get('payments', [PaymentController::class, 'index']);
        Route::get('payments/{id}', [PaymentController::class, 'show']);
        Route::middleware('role:admin,teacher,parent')->group(function () {
            Route::post('payments', [PaymentController::class, 'store']);
        });
        Route::middleware('role:admin')->group(function () {
            Route::put('payments/{id}', [PaymentController::class, 'update']);
            Route::patch('payments/{id}', [PaymentController::class, 'update']);
            Route::delete('payments/{id}', [PaymentController::class, 'destroy']);
        });
    });
    
    // Attendance routes
    Route::middleware('role:admin,teacher,parent,student,driver,cleaner')->group(function () {
        Route::get('attendances', [AttendanceController::class, 'index']);
        Route::get('attendances/{id}', [AttendanceController::class, 'show']);
        Route::middleware('role:admin,teacher,driver,cleaner')->group(function () {
            Route::post('attendances', [AttendanceController::class, 'store']);
            Route::put('attendances/{id}', [AttendanceController::class, 'update']);
            Route::patch('attendances/{id}', [AttendanceController::class, 'update']);
        });
        Route::middleware('role:admin')->group(function () {
            Route::delete('attendances/{id}', [AttendanceController::class, 'destroy']);
        });
    });
    
    // Alert routes
    Route::middleware('role:admin,teacher,parent,student,driver,cleaner')->group(function () {
        Route::get('alerts', [AlertController::class, 'index']);
        Route::get('alerts/{id}', [AlertController::class, 'show']);
        Route::middleware('role:admin,teacher,driver,cleaner')->group(function () {
            Route::post('alerts', [AlertController::class, 'store']);
        });
        Route::middleware('role:admin,teacher')->group(function () {
            Route::put('alerts/{id}', [AlertController::class, 'update']);
            Route::patch('alerts/{id}', [AlertController::class, 'update']);
        });
        Route::middleware('role:admin')->group(function () {
            Route::delete('alerts/{id}', [AlertController::class, 'destroy']);
        });
    });
    
    // Announcement routes
    Route::middleware('role:admin,teacher,parent,student')->group(function () {
        Route::get('announcements', [AnnouncementController::class, 'index']);
        Route::get('announcements/{id}', [AnnouncementController::class, 'show']);
        Route::middleware('role:admin,teacher')->group(function () {
            Route::post('announcements', [AnnouncementController::class, 'store']);
            Route::put('announcements/{id}', [AnnouncementController::class, 'update']);
            Route::patch('announcements/{id}', [AnnouncementController::class, 'update']);
            Route::delete('announcements/{id}', [AnnouncementController::class, 'destroy']);
        });
    });
    
    // Staff-specific routes with permission-based access
    Route::middleware('role:admin,driver,cleaner')->group(function () {
        Route::get('staff-profiles', [StaffController::class, 'index'])->middleware('permission:view_staff');
        Route::get('staff-profiles/{id}', [StaffController::class, 'show'])->middleware('permission:view_staff');
        Route::middleware('role:admin')->group(function () {
            Route::post('staff-profiles', [StaffController::class, 'store'])->middleware('permission:create_staff');
            Route::put('staff-profiles/{id}', [StaffController::class, 'update'])->middleware('permission:edit_staff');
            Route::patch('staff-profiles/{id}', [StaffController::class, 'update'])->middleware('permission:edit_staff');
            Route::delete('staff-profiles/{id}', [StaffController::class, 'destroy'])->middleware('permission:delete_staff');
        });
    });
    
    // Teacher-specific routes with permission-based access
    Route::prefix('teachers')->middleware('role:admin,teacher')->group(function () {
        Route::get('/', [TeacherController::class, 'index'])->middleware('permission:view_users');
        Route::get('/{id}', [TeacherController::class, 'show'])->middleware('permission:view_users');
        Route::middleware('role:admin')->group(function () {
            Route::post('/', [TeacherController::class, 'store'])->middleware('permission:create_users');
            Route::put('/{id}', [TeacherController::class, 'update'])->middleware('permission:edit_users');
            Route::patch('/{id}', [TeacherController::class, 'update'])->middleware('permission:edit_users');
            Route::delete('/{id}', [TeacherController::class, 'destroy'])->middleware('permission:delete_users');
        });
        
        // Teacher-specific routes for their own data
        Route::middleware('role:teacher')->group(function () {
            Route::get('/me/classes', [TeacherController::class, 'getMyClasses']);
            Route::get('/me/students', [TeacherController::class, 'getMyStudents']);
        });
    });
    
    // Parent-specific routes with permission-based access
    Route::prefix('parents')->middleware('role:admin,parent')->group(function () {
        Route::get('/', [ParentController::class, 'index'])->middleware('permission:view_users');
        Route::get('/{id}', [ParentController::class, 'show'])->middleware('permission:view_users');
        Route::middleware('role:admin')->group(function () {
            Route::post('/', [ParentController::class, 'store'])->middleware('permission:create_users');
            Route::put('/{id}', [ParentController::class, 'update'])->middleware('permission:edit_users');
            Route::patch('/{id}', [ParentController::class, 'update'])->middleware('permission:edit_users');
            Route::delete('/{id}', [ParentController::class, 'destroy'])->middleware('permission:delete_users');
        });
        
        // Parent-specific routes for their own data
        Route::middleware('role:parent')->group(function () {
            Route::get('/me/students', [ParentController::class, 'getMyStudents']);
        });
    });
});
});