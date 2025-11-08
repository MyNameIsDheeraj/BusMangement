<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentResource;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\ErrorResource;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\Stop;
use App\Models\ParentModel;
use App\Models\StudentParent;
use OpenApi\Annotations as OA;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/students",
     *     tags={"Students"},
     *     summary="Get list of students",
     *     description="Returns a paginated list of students based on user role",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search students by name, admission number or address",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="class_id",
     *         in="query",
     *         description="Filter students by class ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="academic_year",
     *         in="query",
     *         description="Filter students by academic year",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="bus_service_active",
     *         in="query",
     *         description="Filter students by bus service status",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="user", type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="John Doe")
     *                         ),
     *                         @OA\Property(property="admission_no", type="string", example="STU001")
     *                     )
     *                 ),
     *                 @OA\Property(property="links", type="object",
     *                     @OA\Property(property="first", type="string", example="http://localhost/api/v1/students?page=1"),
     *                     @OA\Property(property="last", type="string", example="http://localhost/api/v1/students?page=10"),
     *                     @OA\Property(property="prev", type="string", example=null),
     *                     @OA\Property(property="next", type="string", example="http://localhost/api/v1/students?page=2")
     *                 ),
     *                 @OA\Property(property="meta", type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="from", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=10),
     *                     @OA\Property(property="links", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="url", type="string", example=null),
     *                             @OA\Property(property="label", type="string", example="&laquo; Previous"),
     *                             @OA\Property(property="active", type="boolean", example=false)
     *                         )
     *                     ),
     *                     @OA\Property(property="path", type="string", example="http://localhost/api/v1/students"),
     *                     @OA\Property(property="per_page", type="integer", example=15),
     *                     @OA\Property(property="to", type="integer", example=15),
     *                     @OA\Property(property="total", type="integer", example=150)
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example=null),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="timestamp", type="string", example="2025-11-04T02:30:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $currentUser = auth('api')->user();
        
        $query = Student::with(['user', 'class', 'pickupStop', 'dropStop', 'parents']);
        
        // Apply search filter if provided
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhere('admission_no', 'LIKE', "%{$search}%")
                ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply class filter if provided
        if ($request->has('class_id')) {
            $query->where('class_id', $request->get('class_id'));
        }
        
        // Apply academic year filter if provided
        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->get('academic_year'));
        }
        
        // Apply bus service status filter if provided
        if ($request->has('bus_service_active')) {
            $query->where('bus_service_active', $request->get('bus_service_active'));
        }
        
        // Admin can see all students
        if ($currentUser->role->name === 'admin') {
            $students = $query->paginate(15);
            return new SuccessResource($students->through(function ($student) {
                return new StudentResource($student);
            }));
        } 
        // Teacher can only see students in their classes
        elseif ($currentUser->role->name === 'teacher') {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $students = $query->whereIn('class_id', $classIds)->paginate(15);
            return new SuccessResource($students->through(function ($student) {
                return new StudentResource($student);
            }));
        } 
        // Parent can see their children
        elseif ($currentUser->role->name === 'parent') {
            $studentIds = $currentUser->parent->students->pluck('id');
            $students = $query->whereIn('id', $studentIds)->paginate(15);
            return new SuccessResource($students->through(function ($student) {
                return new StudentResource($student);
            }));
        } 
        // Student can only see their own information
        elseif ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if (!$student) {
                return new ErrorResource(['message' => 'Student record not found']);
            }
            return new SuccessResource(new StudentResource($student));
        } else {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/students",
     *     tags={"Students"},
     *     summary="Create a new student",
     *     description="Creates a new student record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "class_id", "admission_no", "address", "pickup_stop_id", "drop_stop_id", "bus_service_active", "academic_year"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="class_id", type="integer", example=1),
     *             @OA\Property(property="admission_no", type="string", example="STU001"),
     *             @OA\Property(property="address", type="string", example="123 Main Street, City"),
     *             @OA\Property(property="pickup_stop_id", type="integer", example=1),
     *             @OA\Property(property="drop_stop_id", type="integer", example=2),
     *             @OA\Property(property="bus_service_active", type="boolean", example=true),
     *             @OA\Property(property="academic_year", type="string", example="2025-2026")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Student created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="admission_no", type="string", example="STU001"),
     *                 @OA\Property(property="address", type="string", example="123 Main Street")
     *             ),
     *             @OA\Property(property="message", type="string", example=null),
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(property="timestamp", type="string", example="2025-11-04T02:30:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(StudentRequest $request)
    {
        $currentUser = auth('api')->user();
        
        // Check permission for creating students
        if ($currentUser->role->name !== 'admin' || !$this->hasPermission($currentUser, 'create_student')) {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }

        $student = Student::create($request->validated());

        return new SuccessResource(new StudentResource($student), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $student = Student::with(['user', 'class', 'pickupStop', 'dropStop', 'parents', 'payments', 'attendances', 'leaves', 'alerts'])->findOrFail($id);
        $currentUser = auth('api')->user();
        
        // Check if user can view this student based on permissions
        if (!$this->canViewStudent($student)) {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        return new SuccessResource(new StudentResource($student));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StudentRequest $request, $id)
    {
        $currentUser = auth('api')->user();
        
        $student = Student::findOrFail($id);
        
        // Check permission for updating students
        $canUpdate = false;
        
        if ($currentUser->role->name === 'admin' && $this->hasPermission($currentUser, 'edit_student')) {
            $canUpdate = true;
        } elseif ($currentUser->role->name === 'teacher' && $this->hasPermission($currentUser, 'edit_student')) {
            // Teacher can edit students in their classes
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $canUpdate = $classIds->contains($student->class_id);
        } elseif ($currentUser->role->name === 'parent' && $this->hasPermission($currentUser, 'edit_student')) {
            // Parent can edit their children
            $studentIds = $currentUser->parent->students->pluck('id');
            $canUpdate = $studentIds->contains($student->id);
        }
        
        if (!$canUpdate) {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }

        $student->update($request->validated());

        return new SuccessResource(new StudentResource($student));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $currentUser = auth('api')->user();
        
        // Check permission for deleting students
        if ($currentUser->role->name !== 'admin' || !$this->hasPermission($currentUser, 'delete_student')) {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $student = Student::findOrFail($id);
        $student->delete();

        return new SuccessResource(null, ['message' => 'Student deleted successfully']);
    }
    
    /**
     * Bulk delete students
     */
    public function bulkDelete(Request $request)
    {
        $currentUser = auth('api')->user();
        
        // Check permission for deleting students
        if ($currentUser->role->name !== 'admin' || !$this->hasPermission($currentUser, 'delete_student')) {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return new ErrorResource(['message' => 'No IDs provided'], 422);
        }
        
        // Validate that all IDs are integers
        foreach ($ids as $id) {
            if (!is_numeric($id)) {
                return new ErrorResource(['message' => 'Invalid ID provided'], 422);
            }
        }
        
        Student::whereIn('id', $ids)->delete();
        
        return new SuccessResource(null, ['message' => count($ids) . ' students deleted successfully']);
    }
    
    /**
     * Assign parent to student
     */
    public function assignParent(Request $request, $studentId)
    {
        $currentUser = auth('api')->user();
        
        // Check permission for managing student-parent relationships
        if ($currentUser->role->name !== 'admin' || !$this->hasPermission($currentUser, 'edit_student')) {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'parent_id' => 'required|exists:parents,id',
        ]);

        if ($validator->fails()) {
            return new ErrorResource(['message' => $validator->errors()], 422);
        }
        
        $student = Student::findOrFail($studentId);
        $parent = ParentModel::findOrFail($request->parent_id);
        
        // Check if relationship already exists
        $existing = StudentParent::where('student_id', $studentId)
                             ->where('parent_id', $request->parent_id)
                             ->first();
        
        if ($existing) {
            return new ErrorResource(['message' => 'Parent already assigned to this student'], 422);
        }
        
        // Create the relationship
        $student->parents()->attach($request->parent_id);
        
        return new SuccessResource(null, ['message' => 'Parent assigned to student successfully'], 201);
    }
    
    /**
     * Remove parent from student
     */
    public function removeParent($studentId, $parentId)
    {
        $currentUser = auth('api')->user();
        
        // Check permission for managing student-parent relationships
        if ($currentUser->role->name !== 'admin' || !$this->hasPermission($currentUser, 'edit_student')) {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $student = Student::findOrFail($studentId);
        $parent = ParentModel::findOrFail($parentId);
        
        // Remove the relationship
        $student->parents()->detach($parentId);
        
        return new SuccessResource(null, ['message' => 'Parent removed from student successfully']);
    }
    
    /**
     * Get all students for a class
     */
    public function studentsByClass($classId)
    {
        $currentUser = auth('api')->user();
        
        // Check if user can view students in this class
        if (!$this->canViewStudentsInClass($classId)) {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $students = Student::with(['user', 'pickupStop', 'dropStop', 'parents'])
                          ->where('class_id', $classId)
                          ->get();
        
        return new SuccessResource($students->map(function ($student) {
            return new StudentResource($student);
        }));
    }
    
    /**
     * Check if current user can view the specified student
     */
    private function canViewStudent($student)
    {
        $currentUser = auth('api')->user();
        
        // Admins can view all students
        if ($currentUser->role->name === 'admin' && $this->hasPermission($currentUser, 'view_student')) {
            return true;
        }
        
        // Teachers can view students in their classes
        if ($currentUser->role->name === 'teacher' && $this->hasPermission($currentUser, 'view_student')) {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            return $classIds->contains($student->class_id);
        }
        
        // Parents can view their children
        if ($currentUser->role->name === 'parent' && $this->hasPermission($currentUser, 'view_student')) {
            $studentIds = $currentUser->parent->students->pluck('id');
            return $studentIds->contains($student->id);
        }
        
        // Students can view themselves
        if ($currentUser->role->name === 'student' && $currentUser->student && 
            $currentUser->student->id === $student->id && $this->hasPermission($currentUser, 'view_student')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if current user can view students in a specific class
     */
    private function canViewStudentsInClass($classId)
    {
        $currentUser = auth('api')->user();
        
        // Admins can view students in any class
        if ($currentUser->role->name === 'admin' && $this->hasPermission($currentUser, 'view_student')) {
            return true;
        }
        
        // Teachers can view students in their classes
        if ($currentUser->role->name === 'teacher' && $this->hasPermission($currentUser, 'view_student')) {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            return $classIds->contains($classId);
        }
        
        return false;
    }
    
    /**
     * Check if user has a specific permission
     */
    private function hasPermission($user, $permission)
    {
        $userPermissions = $user->role->permissions->pluck('name')->toArray();
        return in_array($permission, $userPermissions);
    }
}
