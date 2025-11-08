<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stop;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StopController extends Controller
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
        
        $query = Stop::with(['route']);
        
        // Admin can see all stops
        if ($currentUser->role->name === 'admin') {
            $stops = $query->paginate(15);
        } 
        // Driver can only see stops for their assigned bus
        elseif ($currentUser->role->name === 'driver') {
            $busIds = \App\Models\Bus::where('driver_id', $currentUser->id)->pluck('id');
            $routeIds = Route::whereIn('bus_id', $busIds)->pluck('id');
            $stops = $query->whereIn('route_id', $routeIds)->paginate(15);
        } 
        // Cleaner can only see stops for their assigned bus
        elseif ($currentUser->role->name === 'cleaner') {
            $busIds = \App\Models\Bus::where('cleaner_id', $currentUser->id)->pluck('id');
            $routeIds = Route::whereIn('bus_id', $busIds)->pluck('id');
            $stops = $query->whereIn('route_id', $routeIds)->paginate(15);
        } 
        // Teacher can see stops related to their classes
        elseif ($currentUser->role->name === 'teacher') {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $studentIds = \App\Models\Student::whereIn('class_id', $classIds)->pluck('id');
            $stops = $query->whereIn('id', \App\Models\StudentRoute::whereIn('student_id', $studentIds)->pluck('stop_id'))->paginate(15);
        } 
        // Parent can see stops for their children
        elseif ($currentUser->role->name === 'parent') {
            $studentIds = $currentUser->parent->students->pluck('id');
            $stops = $query->whereIn('id', \App\Models\StudentRoute::whereIn('student_id', $studentIds)->pluck('stop_id'))->paginate(15);
        } 
        // Student can see their stops
        elseif ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if (!$student) {
                return response()->json(['error' => 'Student record not found'], 404);
            }
            $stopIds = [$student->pickup_stop_id, $student->drop_stop_id];
            $stops = $query->whereIn('id', $stopIds)->get();
            return response()->json($stops);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($stops);
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
        
        // Only admin can create stops
        if ($currentUser->role->name !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'route_id' => 'required|exists:routes,id',
            'name' => 'required|string',
            'pickup_time' => 'nullable|date_format:H:i',
            'drop_time' => 'nullable|date_format:H:i',
            'distance_from_start_km' => 'required|numeric',
            'academic_year' => 'required|regex:/^\d{4}-\d{4}$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $stop = Stop::create($request->all());

        return response()->json($stop, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $stop = Stop::with(['route'])->findOrFail($id);
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if user can view this stop
        if (!$this->canViewStop($stop)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($stop);
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
        
        // Only admin can update stops
        if ($currentUser->role->name !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $stop = Stop::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'route_id' => 'sometimes|required|exists:routes,id',
            'name' => 'sometimes|required|string',
            'pickup_time' => 'sometimes|nullable|date_format:H:i',
            'drop_time' => 'sometimes|nullable|date_format:H:i',
            'distance_from_start_km' => 'sometimes|required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $stop->update($request->all());

        return response()->json($stop);
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
        
        // Only admin can delete stops
        if ($currentUser->role->name !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $stop = Stop::findOrFail($id);
        $stop->delete();

        return response()->json(['message' => 'Stop deleted successfully']);
    }
    
    /**
     * Check if current user can view the specified stop
     */
    private function canViewStop($stop)
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return false;
        }
        
        // Admins can view all stops
        if ($currentUser->role->name === 'admin') {
            return true;
        }
        
        // Driver can view stops for their assigned bus
        if ($currentUser->role->name === 'driver') {
            $busIds = \App\Models\Bus::where('driver_id', $currentUser->id)->pluck('id');
            $routeIds = Route::whereIn('bus_id', $busIds)->pluck('id');
            return $routeIds->contains($stop->route_id);
        }
        
        // Cleaner can view stops for their assigned bus
        if ($currentUser->role->name === 'cleaner') {
            $busIds = \App\Models\Bus::where('cleaner_id', $currentUser->id)->pluck('id');
            $routeIds = Route::whereIn('bus_id', $busIds)->pluck('id');
            return $routeIds->contains($stop->route_id);
        }
        
        // Teacher can view stops related to their classes
        if ($currentUser->role->name === 'teacher') {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $studentIds = \App\Models\Student::whereIn('class_id', $classIds)->pluck('id');
            $stopIds = \App\Models\StudentRoute::whereIn('student_id', $studentIds)->pluck('stop_id');
            return $stopIds->contains($stop->id);
        }
        
        // Parent can view stops for their children
        if ($currentUser->role->name === 'parent') {
            $studentIds = $currentUser->parent->students->pluck('id');
            $stopIds = \App\Models\StudentRoute::whereIn('student_id', $studentIds)->pluck('stop_id');
            return $stopIds->contains($stop->id);
        }
        
        // Student can view their stops
        if ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if (!$student) {
                return false;
            }
            $stopIds = [$student->pickup_stop_id, $student->drop_stop_id];
            return collect($stopIds)->contains($stop->id);
        }
        
        return false;
    }
}
