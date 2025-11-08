<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeacherRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    /**
     * Display all teachers with their assigned classes and student counts.
     */
    public function index()
    {
        $currentUser = auth('api')->user();
        
        $query = User::with(['role', 'classTeacher'])
            ->whereHas('role', function($query) {
                $query->where('name', 'teacher');
            });
        
        // Only admin can view all teachers
        if ($currentUser->role->name !== 'admin') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $teachers = $query->paginate(15);
        
        // Add additional data like student count per teacher
        $teachers->getCollection()->transform(function($teacher) {
            $classIds = $teacher->classTeacher ? $teacher->classTeacher->pluck('id') : collect();
            $teacher->student_count = Student::whereIn('class_id', $classIds)->count();
            return $teacher;
        });
        
        return new SuccessResource($teachers->through(function ($teacher) {
            return new UserResource($teacher);
        }));
    }

    /**
     * Display a specific teacher with detailed information.
     */
    public function show($id)
    {
        $currentUser = auth('api')->user();
        
        $teacher = User::with(['role', 'classTeacher', 'busesAsDriver', 'busesAsCleaner'])->findOrFail($id);
        
        // Admin can view any teacher
        if ($currentUser->role->name === 'admin') {
            return new SuccessResource(new UserResource($teacher));
        }
        
        // Teachers can view their own profile
        if ($currentUser->role->name === 'teacher' && $currentUser->id == $id) {
            return new SuccessResource(new UserResource($teacher));
        }
        
        // Teachers can view other teachers in the same class context
        if ($currentUser->role->name === 'teacher') {
            $currentClasses = ClassModel::where('class_teacher_id', $currentUser->id)->pluck('id');
            $targetClasses = ClassModel::where('class_teacher_id', $id)->pluck('id');
            
            if ($currentClasses->intersect($targetClasses)->count() > 0) {
                return new SuccessResource(new UserResource($teacher));
            }
        }
        
        // Parents can view teachers of their children
        if ($currentUser->role->name === 'parent') {
            $parentModel = \App\Models\ParentModel::where('user_id', $currentUser->id)->first();
            if ($parentModel) {
                $students = $parentModel->students;
                $studentClassIds = $students->pluck('class_id');
                
                $teacherClassIds = $teacher->classTeacher ? $teacher->classTeacher->pluck('id') : collect();
                if ($studentClassIds->intersect($teacherClassIds)->count() > 0) {
                    return new SuccessResource(new UserResource($teacher));
                }
            }
        }
        
        // Students can view their class teacher
        if ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if ($student) {
                $classes = ClassModel::where('class_teacher_id', $id)->pluck('id');
                if ($classes->contains($student->class_id)) {
                    return new SuccessResource(new UserResource($teacher));
                }
            }
        }
        
        return new ErrorResource(['message' => 'Unauthorized'], 403);
    }

    /**
     * Get classes assigned to the current teacher.
     */
    public function getMyClasses()
    {
        $currentUser = auth('api')->user();
        
        if ($currentUser->role->name !== 'teacher') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $classes = ClassModel::where('class_teacher_id', $currentUser->id)
            ->with(['students', 'students.user'])
            ->get();
        
        return new SuccessResource($classes);
    }

    /**
     * Get students for the current teacher's classes.
     */
    public function getMyStudents()
    {
        $currentUser = auth('api')->user();
        
        if ($currentUser->role->name !== 'teacher') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $classIds = ClassModel::where('class_teacher_id', $currentUser->id)->pluck('id');
        $students = Student::whereIn('class_id', $classIds)
            ->with(['user', 'class', 'parents'])
            ->get();
        
        return new SuccessResource($students->map(function ($student) {
            return new \App\Http\Resources\StudentResource($student);
        }));
    }

    /**
     * Store a new teacher (admin only).
     */
    public function store(TeacherRequest $request)
    {
        $currentUser = auth('api')->user();
        
        if ($currentUser->role->name !== 'admin') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }

        $teacher = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'mobile' => $request->mobile,
            'role_id' => \App\Models\Role::where('name', 'teacher')->first()->id,
        ]);

        $teacher->load(['role', 'classTeacher']);
        return new SuccessResource(new UserResource($teacher), 201);
    }

    /**
     * Update an existing teacher (specific permissions).
     */
    public function update(TeacherRequest $request, $id)
    {
        $currentUser = auth('api')->user();
        
        $teacher = User::findOrFail($id);
        
        // Admin can update any teacher
        if ($currentUser->role->name === 'admin') {
            $data = $request->only(['name', 'email', 'mobile']);
            
            if ($request->has('password')) {
                $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            }

            $teacher->update($data);
            $teacher->load(['role', 'classTeacher']);

            return new SuccessResource(new UserResource($teacher));
        }
        
        // Teachers can update their own profile
        if ($currentUser->role->name === 'teacher' && $currentUser->id == $id) {
            $data = $request->only(['name', 'email', 'mobile']);
            
            if ($request->has('password')) {
                $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            }

            $teacher->update($data);
            $teacher->load(['role', 'classTeacher']);

            return new SuccessResource(new UserResource($teacher));
        }
        
        return new ErrorResource(['message' => 'Unauthorized'], 403);
    }

    /**
     * Delete a teacher (admin only).
     */
    public function destroy($id)
    {
        $currentUser = auth('api')->user();
        
        // Only admin can delete a teacher
        if ($currentUser->role->name !== 'admin') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $teacher = User::findOrFail($id);
        $teacher->delete();

        return new SuccessResource(null, ['message' => 'Teacher deleted successfully']);
    }
}