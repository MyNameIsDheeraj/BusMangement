<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Student;
use App\Models\Bus;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $query = Alert::with(['submitter', 'student.user', 'bus', 'route']);
        
        // Admin can see all alerts
        if ($currentUser->role->name === 'admin') {
            $alerts = $query->paginate(15);
        } 
        // Teacher can see alerts related to their classes
        elseif ($currentUser->role->name === 'teacher') {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $studentIds = Student::whereIn('class_id', $classIds)->pluck('id');
            $alerts = $query->whereIn('student_id', $studentIds)->paginate(15);
        } 
        // Driver can see alerts related to their bus
        elseif ($currentUser->role->name === 'driver') {
            $busId = Bus::where('driver_id', $currentUser->id)->pluck('id')->first();
            $routeIds = Route::where('bus_id', $busId)->pluck('id');
            $stopIds = \App\Models\Stop::whereIn('route_id', $routeIds)->pluck('id');
            $studentIds = \App\Models\StudentRoute::whereIn('stop_id', $stopIds)->pluck('student_id');
            $alerts = $query->whereIn('student_id', $studentIds)->orWhere('bus_id', $busId)->orWhereIn('route_id', $routeIds)->paginate(15);
        } 
        // Cleaner can see alerts related to their bus
        elseif ($currentUser->role->name === 'cleaner') {
            $busId = Bus::where('cleaner_id', $currentUser->id)->pluck('id')->first();
            $routeIds = Route::where('bus_id', $busId)->pluck('id');
            $stopIds = \App\Models\Stop::whereIn('route_id', $routeIds)->pluck('id');
            $studentIds = \App\Models\StudentRoute::whereIn('stop_id', $stopIds)->pluck('student_id');
            $alerts = $query->whereIn('student_id', $studentIds)->orWhere('bus_id', $busId)->orWhereIn('route_id', $routeIds)->paginate(15);
        } 
        // Parent can see alerts for their children
        elseif ($currentUser->role->name === 'parent') {
            $studentIds = $currentUser->parent->students->pluck('id');
            $alerts = $query->whereIn('student_id', $studentIds)->paginate(15);
        } 
        // Student can see their own alerts
        elseif ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if (!$student) {
                return response()->json(['error' => 'Student record not found'], 404);
            }
            $alerts = $query->where('student_id', $student->id)->get();
            return response()->json($alerts);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($alerts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Only admin, teacher, driver, and cleaner can create alerts
        if (!in_array($currentUser->role->name, ['admin', 'teacher', 'driver', 'cleaner'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high',
            'bus_id' => 'nullable|exists:buses,id',
            'route_id' => 'nullable|exists:routes,id',
            'media_path' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Add the submitting user ID
        $request['submitted_by'] = $currentUser->id;
        
        $alert = Alert::create($request->all());

        return response()->json($alert, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $alert = Alert::with(['submitter', 'student.user', 'bus', 'route'])->findOrFail($id);
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if user can view this alert
        if (!$this->canViewAlert($alert)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($alert);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Only admin can update alerts
        if ($currentUser->role->name !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $alert = Alert::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'student_id' => 'sometimes|required|exists:students,id',
            'description' => 'sometimes|required|string',
            'severity' => 'sometimes|required|in:low,medium,high',
            'bus_id' => 'sometimes|nullable|exists:buses,id',
            'route_id' => 'sometimes|nullable|exists:routes,id',
            'media_path' => 'sometimes|nullable|string',
            'status' => 'sometimes|required|in:new,read,resolved',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $alert->update($request->all());

        return response()->json($alert);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Only admin can delete alerts
        if ($currentUser->role->name !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $alert = Alert::findOrFail($id);
        $alert->delete();

        return response()->json(['message' => 'Alert deleted successfully']);
    }
    
    /**
     * Check if current user can view the specified alert
     */
    private function canViewAlert($alert)
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return false;
        }
        
        // Admins can view all alerts
        if ($currentUser->role->name === 'admin') {
            return true;
        }
        
        // Teachers can view alerts for students in their classes
        if ($currentUser->role->name === 'teacher') {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $studentIds = Student::whereIn('class_id', $classIds)->pluck('id');
            return $studentIds->contains($alert->student_id);
        }
        
        // Driver can view alerts for their bus or related to students on their route
        if ($currentUser->role->name === 'driver') {
            $busId = \App\Models\Bus::where('driver_id', $currentUser->id)->pluck('id')->first();
            if ($alert->bus_id == $busId) {
                return true;
            }
            
            // Check if alert is for a student on the driver's bus route
            $routeIds = Route::where('bus_id', $busId)->pluck('id');
            $stopIds = \App\Models\Stop::whereIn('route_id', $routeIds)->pluck('id');
            $studentIds = \App\Models\StudentRoute::whereIn('stop_id', $stopIds)->pluck('student_id');
            return $studentIds->contains($alert->student_id);
        }
        
        // Cleaner can view alerts for their bus or related to students on their route
        if ($currentUser->role->name === 'cleaner') {
            $busId = \App\Models\Bus::where('cleaner_id', $currentUser->id)->pluck('id')->first();
            if ($alert->bus_id == $busId) {
                return true;
            }
            
            // Check if alert is for a student on the cleaner's bus route
            $routeIds = Route::where('bus_id', $busId)->pluck('id');
            $stopIds = \App\Models\Stop::whereIn('route_id', $routeIds)->pluck('id');
            $studentIds = \App\Models\StudentRoute::whereIn('stop_id', $stopIds)->pluck('student_id');
            return $studentIds->contains($alert->student_id);
        }
        
        // Parents can view alerts for their children
        if ($currentUser->role->name === 'parent') {
            $studentIds = $currentUser->parent->students->pluck('id');
            return $studentIds->contains($alert->student_id);
        }
        
        // Students can view their own alerts
        if ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if (!$student) {
                return false;
            }
            return $student->id === $alert->student_id;
        }
        
        return false;
    }
}
