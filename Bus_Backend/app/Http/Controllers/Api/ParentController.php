<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ParentRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\ParentModel;
use App\Models\Student;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    /**
     * Display all parents with their associated students.
     */
    public function index()
    {
        $currentUser = auth('api')->user();
        
        $query = User::with(['role', 'parent', 'parent.students', 'parent.students.user'])
            ->whereHas('role', function($query) {
                $query->where('name', 'parent');
            });
        
        // Only admin can view all parents
        if ($currentUser->role->name !== 'admin') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $parents = $query->paginate(15);
        
        // Add additional data like student count per parent
        $parents->getCollection()->transform(function($parent) {
            $parent->student_count = $parent->parent ? $parent->parent->students->count() : 0;
            return $parent;
        });
        
        return new SuccessResource($parents->through(function ($parent) {
            return new UserResource($parent);
        }));
    }

    /**
     * Display a specific parent with detailed information.
     */
    public function show($id)
    {
        $currentUser = auth('api')->user();
        
        $query = User::with(['role', 'parent', 'parent.students', 'parent.students.user']);
        
        // Admin can view any parent
        if ($currentUser->role->name === 'admin') {
            $parent = $query->findOrFail($id);
            return new SuccessResource(new UserResource($parent));
        }
        
        // Parents can view their own profile
        if ($currentUser->role->name === 'parent' && $currentUser->id == $id) {
            $parent = $query->findOrFail($id);
            return new SuccessResource(new UserResource($parent));
        }
        
        // Teachers can view parents of students in their classes
        if ($currentUser->role->name === 'teacher') {
            $classIds = \App\Models\ClassModel::where('class_teacher_id', $currentUser->id)->pluck('id');
            $studentIds = \App\Models\Student::whereIn('class_id', $classIds)->pluck('user_id');
            $parentModel = \App\Models\ParentModel::where('user_id', $id)->first();
            
            if ($parentModel && $studentIds->contains($parentModel->students->pluck('user_id'))) {
                $parent = $query->findOrFail($id);
                return new SuccessResource(new UserResource($parent));
            }
        }
        
        // Students can view their parent
        if ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if ($student) {
                $parentModel = \App\Models\ParentModel::where('user_id', $id)->first();
                if ($parentModel) {
                    $studentIds = $parentModel->students->pluck('user_id');
                    if ($studentIds->contains($currentUser->id)) {
                        $parent = $query->findOrFail($id);
                        return new SuccessResource(new UserResource($parent));
                    }
                }
            }
        }
        
        return new ErrorResource(['message' => 'Unauthorized'], 403);
    }

    /**
     * Get students associated with the current parent.
     */
    public function getMyStudents()
    {
        $currentUser = auth('api')->user();
        
        if ($currentUser->role->name !== 'parent') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $parentModel = ParentModel::where('user_id', $currentUser->id)->first();
        
        if (!$parentModel) {
            return new ErrorResource(['message' => 'Parent profile not found'], 404);
        }
        
        $students = $parentModel->students()->with(['user', 'class', 'pickupStop', 'dropStop'])->get();
        
        return new SuccessResource($students->map(function ($student) {
            return new \App\Http\Resources\StudentResource($student);
        }));
    }

    /**
     * Store a new parent (admin only).
     */
    public function store(ParentRequest $request)
    {
        $currentUser = auth('api')->user();
        
        if ($currentUser->role->name !== 'admin') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }

        $parent = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'mobile' => $request->mobile,
            'role_id' => \App\Models\Role::where('name', 'parent')->first()->id,
        ]);

        // Create parent model record
        $parentModel = ParentModel::create([
            'user_id' => $parent->id
        ]);

        $parent->load(['role', 'parent', 'parent.students']);
        return new SuccessResource(new UserResource($parent), 201);
    }

    /**
     * Update an existing parent (specific permissions).
     */
    public function update(ParentRequest $request, $id)
    {
        $currentUser = auth('api')->user();
        
        $parent = User::findOrFail($id);
        
        // Admin can update any parent
        if ($currentUser->role->name === 'admin') {
            $data = $request->only(['name', 'email', 'mobile']);
            
            if ($request->has('password')) {
                $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            }

            $parent->update($data);
            $parent->load(['role', 'parent', 'parent.students']);

            return new SuccessResource(new UserResource($parent));
        }
        
        // Parents can update their own profile
        if ($currentUser->role->name === 'parent' && $currentUser->id == $id) {
            $data = $request->only(['name', 'email', 'mobile']);
            
            if ($request->has('password')) {
                $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            }

            $parent->update($data);
            $parent->load(['role', 'parent', 'parent.students']);

            return new SuccessResource(new UserResource($parent));
        }
        
        return new ErrorResource(['message' => 'Unauthorized'], 403);
    }

    /**
     * Delete a parent (admin only).
     */
    public function destroy($id)
    {
        $currentUser = auth('api')->user();
        
        // Only admin can delete a parent
        if ($currentUser->role->name !== 'admin') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $parent = User::findOrFail($id);
        $parentModel = ParentModel::where('user_id', $id)->first();
        
        if ($parentModel) {
            $parentModel->delete();
        }
        
        $parent->delete();

        return new SuccessResource(null, ['message' => 'Parent deleted successfully']);
    }
}