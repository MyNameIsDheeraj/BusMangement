<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentParentRequest;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\StudentParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentParentController extends Controller
{
    /**
     * Display all parent-student relationships.
     */
    public function index(Request $request)
    {
        $currentUser = auth('api')->user();
        
        // Check if user has permission to view student-parent relationships
        if (!$this->hasPermission($currentUser, 'view_student')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $query = StudentParent::with(['student.user', 'parent.user']);
        
        // Filter by student ID if provided
        if ($request->has('student_id')) {
            $query->where('student_id', $request->get('student_id'));
        }
        
        // Filter by parent ID if provided
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->get('parent_id'));
        }
        
        // Limit results based on user role
        if ($currentUser->role->name === 'teacher') {
            // Teacher can only see relationships for students in their classes
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $query->whereHas('student', function($q) use ($classIds) {
                $q->whereIn('class_id', $classIds);
            });
        } elseif ($currentUser->role->name === 'parent') {
            // Parent can only see their own relationships
            $query->where('parent_id', $currentUser->parent->id);
        }
        
        $relationships = $query->paginate(15);
        
        return response()->json($relationships);
    }

    /**
     * Create a new parent-student relationship.
     */
    public function store(StudentParentRequest $request)
    {
        $currentUser = auth('api')->user();
        
        // Only admin can create parent-student relationships
        if ($currentUser->role->name !== 'admin' || !$this->hasPermission($currentUser, 'edit_student')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if relationship already exists
        $existing = StudentParent::where('student_id', $request->student_id)
                             ->where('parent_id', $request->parent_id)
                             ->first();
        
        if ($existing) {
            return response()->json(['error' => 'Parent-student relationship already exists'], 422);
        }
        
        $relationship = StudentParent::create($request->validated());
        
        return response()->json($relationship, 201);
    }

    /**
     * Display a specific parent-student relationship.
     */
    public function show($id)
    {
        $relationship = StudentParent::with(['student.user', 'parent.user'])->findOrFail($id);
        $currentUser = auth('api')->user();
        
        // Check if user has permission to view this relationship
        if (!$this->canViewRelationship($relationship)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($relationship);
    }

    /**
     * Remove a parent-student relationship.
     */
    public function destroy($id)
    {
        $currentUser = auth('api')->user();
        
        // Only admin can delete parent-student relationships
        if ($currentUser->role->name !== 'admin' || !$this->hasPermission($currentUser, 'delete_student')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $relationship = StudentParent::findOrFail($id);
        $relationship->delete();

        return response()->json(['message' => 'Parent-student relationship removed successfully']);
    }
    
    /**
     * Get all parents for a specific student.
     */
    public function getParentsForStudent($studentId)
    {
        $student = Student::findOrFail($studentId);
        $currentUser = auth('api')->user();
        
        // Check if user has permission to view this student's parents
        if (!$this->canViewStudent($student)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $parents = $student->parents()->with('user')->get();
        
        return response()->json($parents);
    }
    
    /**
     * Get all students for a specific parent.
     */
    public function getStudentsForParent($parentId)
    {
        $parent = ParentModel::findOrFail($parentId);
        $currentUser = auth('api')->user();
        
        // Check if user has permission to view this parent's students
        if (!$this->canViewParent($parent)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $students = $parent->students()->with(['user', 'class'])->get();
        
        return response()->json($students);
    }
    
    /**
     * Check if current user can view a specific relationship
     */
    private function canViewRelationship($relationship)
    {
        $currentUser = auth('api')->user();
        
        // Admin can view all relationships
        if ($currentUser->role->name === 'admin' && $this->hasPermission($currentUser, 'view_student')) {
            return true;
        }
        
        // Teacher can view relationships for students in their classes
        if ($currentUser->role->name === 'teacher' && $this->hasPermission($currentUser, 'view_student')) {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            return $classIds->contains($relationship->student->class_id);
        }
        
        // Parent can view their own relationships
        if ($currentUser->role->name === 'parent' && $this->hasPermission($currentUser, 'view_student')) {
            return $currentUser->parent->id === $relationship->parent_id;
        }
        
        return false;
    }
    
    /**
     * Check if current user can view a specific student
     */
    private function canViewStudent($student)
    {
        $currentUser = auth('api')->user();
        
        // Admin can view any student
        if ($currentUser->role->name === 'admin' && $this->hasPermission($currentUser, 'view_student')) {
            return true;
        }
        
        // Teacher can view students in their classes
        if ($currentUser->role->name === 'teacher' && $this->hasPermission($currentUser, 'view_student')) {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            return $classIds->contains($student->class_id);
        }
        
        // Parent can view their own children
        if ($currentUser->role->name === 'parent' && $this->hasPermission($currentUser, 'view_student')) {
            $studentIds = $currentUser->parent->students->pluck('id');
            return $studentIds->contains($student->id);
        }
        
        // Student can view themselves
        if ($currentUser->role->name === 'student' && $currentUser->student && 
            $currentUser->student->id === $student->id && $this->hasPermission($currentUser, 'view_student')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if current user can view a specific parent
     */
    private function canViewParent($parent)
    {
        $currentUser = auth('api')->user();
        
        // Admin can view any parent
        if ($currentUser->role->name === 'admin' && $this->hasPermission($currentUser, 'view_student')) {
            return true;
        }
        
        // Parent can view themselves
        if ($currentUser->role->name === 'parent' && $this->hasPermission($currentUser, 'view_student')) {
            return $currentUser->parent->id === $parent->id;
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