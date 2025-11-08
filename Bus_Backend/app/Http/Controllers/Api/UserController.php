<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth('api')->user();
        if (!$user || !$user->role || !in_array('view_users', $user->role->permissions()->pluck('name')->toArray())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $users = User::with('role')->paginate(15);
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();
        if (!$user || !$user->role || !in_array('create_users', $user->role->permissions()->pluck('name')->toArray())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'mobile' => 'nullable|string|max:15',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile' => $request->mobile,
            'role_id' => $request->role_id,
        ]);

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);
        
        // Check if user can view this specific user
        if (!$this->canViewUser($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth('api')->user();
        if (!$user || !$user->role || !in_array('edit_users', $user->role->permissions()->pluck('name')->toArray())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|min:6',
            'mobile' => 'sometimes|nullable|string|max:15',
            'role_id' => 'sometimes|required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'email', 'mobile', 'role_id']);
        
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth('api')->user();
        if (!$user || !$user->role || !in_array('delete_users', $user->role->permissions()->pluck('name')->toArray())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
    
    /**
     * Check if current user can view the specified user
     */
    private function canViewUser(User $user)
    {
        $currentUser = auth('api')->user();
        
        // Admins can view all users
        if ($currentUser->role->name === 'admin') {
            return true;
        }
        
        // Users can view themselves
        if ($currentUser->id === $user->id) {
            return true;
        }
        
        // Teachers can view students in their classes
        if ($currentUser->role->name === 'teacher' && $user->role->name === 'student') {
            // Check if the student is in one of the teacher's classes
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            if ($user->student && $classIds->contains($user->student->class_id)) {
                return true;
            }
        }
        
        // Parents can view their children
        if ($currentUser->role->name === 'parent' && $user->role->name === 'student') {
            // Check if the student is one of the parent's children
            $studentIds = $currentUser->parent->students->pluck('id');
            if ($user->student && $studentIds->contains($user->student->id)) {
                return true;
            }
        }
        
        return false;
    }
}
