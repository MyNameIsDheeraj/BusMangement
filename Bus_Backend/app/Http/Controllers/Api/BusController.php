<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusRequest;
use App\Http\Resources\BusResource;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\ErrorResource;
use App\Models\Bus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentUser = auth('api')->user();
        
        $query = Bus::with(['driver', 'cleaner', 'routes']);
        
        // Admin can see all buses
        if ($currentUser->role->name === 'admin') {
            $buses = $query->paginate(15);
            return new SuccessResource($buses->through(function ($bus) {
                return new BusResource($bus);
            }));
        } 
        // Driver can only see their assigned bus
        elseif ($currentUser->role->name === 'driver') {
            $buses = $query->where('driver_id', $currentUser->id)->paginate(15);
            return new SuccessResource($buses->through(function ($bus) {
                return new BusResource($bus);
            }));
        } 
        // Cleaner can only see their assigned bus
        elseif ($currentUser->role->name === 'cleaner') {
            $buses = $query->where('cleaner_id', $currentUser->id)->paginate(15);
            return new SuccessResource($buses->through(function ($bus) {
                return new BusResource($bus);
            }));
        } 
        // Teacher can see buses related to their classes via students
        elseif ($currentUser->role->name === 'teacher') {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $studentIds = \App\Models\Student::whereIn('class_id', $classIds)->pluck('id');
            $stopIds = \App\Models\StudentRoute::whereIn('student_id', $studentIds)->pluck('stop_id');
            $routeIds = \App\Models\Stop::whereIn('id', $stopIds)->pluck('route_id');
            $buses = $query->whereIn('id', \App\Models\Route::whereIn('id', $routeIds)->pluck('bus_id'))->paginate(15);
            return new SuccessResource($buses->through(function ($bus) {
                return new BusResource($bus);
            }));
        } 
        // Parent can see buses for their children
        elseif ($currentUser->role->name === 'parent') {
            $studentIds = $currentUser->parent->students->pluck('id');
            $stopIds = \App\Models\StudentRoute::whereIn('student_id', $studentIds)->pluck('stop_id');
            $routeIds = \App\Models\Stop::whereIn('id', $stopIds)->pluck('route_id');
            $buses = $query->whereIn('id', \App\Models\Route::whereIn('id', $routeIds)->pluck('bus_id'))->paginate(15);
            return new SuccessResource($buses->through(function ($bus) {
                return new BusResource($bus);
            }));
        } 
        // Student can see their assigned bus
        elseif ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if (!$student) {
                return new ErrorResource(['message' => 'Student record not found'], 404);
            }
            $stopIds = [$student->pickup_stop_id, $student->drop_stop_id];
            $routeIds = \App\Models\Stop::whereIn('id', $stopIds)->pluck('route_id');
            $buses = $query->whereIn('id', \App\Models\Route::whereIn('id', $routeIds)->pluck('bus_id'))->get();
            return new SuccessResource($buses->map(function ($bus) {
                return new BusResource($bus);
            }));
        } else {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BusRequest $request)
    {
        $currentUser = auth('api')->user();
        
        // Only admin can create buses
        if ($currentUser->role->name !== 'admin') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }

        $bus = Bus::create($request->validated());

        return new SuccessResource(new BusResource($bus), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bus = Bus::with(['driver', 'cleaner', 'routes.stops'])->findOrFail($id);
        $currentUser = auth('api')->user();
        
        // Check if user can view this bus
        if (!$this->canViewBus($bus)) {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        return new SuccessResource(new BusResource($bus));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BusRequest $request, $id)
    {
        $currentUser = auth('api')->user();
        
        // Only admin can update buses
        if ($currentUser->role->name !== 'admin') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $bus = Bus::findOrFail($id);

        $bus->update($request->validated());

        return new SuccessResource(new BusResource($bus));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $currentUser = auth('api')->user();
        
        // Only admin can delete buses
        if ($currentUser->role->name !== 'admin') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $bus = Bus::findOrFail($id);
        $bus->delete();

        return new SuccessResource(null, ['message' => 'Bus deleted successfully']);
    }
    
    /**
     * Check if current user can view the specified bus
     */
    private function canViewBus($bus)
    {
        $currentUser = auth('api')->user();
        
        // Admins can view all buses
        if ($currentUser->role->name === 'admin') {
            return true;
        }
        
        // Driver can view their assigned bus
        if ($currentUser->role->name === 'driver' && $bus->driver_id == $currentUser->id) {
            return true;
        }
        
        // Cleaner can view their assigned bus
        if ($currentUser->role->name === 'cleaner' && $bus->cleaner_id == $currentUser->id) {
            return true;
        }
        
        // Teacher can view buses related to their classes
        if ($currentUser->role->name === 'teacher') {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $studentIds = \App\Models\Student::whereIn('class_id', $classIds)->pluck('id');
            $stopIds = \App\Models\StudentRoute::whereIn('student_id', $studentIds)->pluck('stop_id');
            $routeIds = \App\Models\Stop::whereIn('id', $stopIds)->pluck('route_id');
            $busIds = \App\Models\Route::whereIn('id', $routeIds)->pluck('bus_id');
            return $busIds->contains($bus->id);
        }
        
        // Parent can view buses for their children
        if ($currentUser->role->name === 'parent') {
            $studentIds = $currentUser->parent->students->pluck('id');
            $stopIds = \App\Models\StudentRoute::whereIn('student_id', $studentIds)->pluck('stop_id');
            $routeIds = \App\Models\Stop::whereIn('id', $stopIds)->pluck('route_id');
            $busIds = \App\Models\Route::whereIn('id', $routeIds)->pluck('bus_id');
            return $busIds->contains($bus->id);
        }
        
        // Student can view their assigned bus
        if ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if (!$student) {
                return false;
            }
            $stopIds = [$student->pickup_stop_id, $student->drop_stop_id];
            $routeIds = \App\Models\Stop::whereIn('id', $stopIds)->pluck('route_id');
            $busIds = \App\Models\Route::whereIn('id', $routeIds)->pluck('bus_id');
            return $busIds->contains($bus->id);
        }
        
        return false;
    }
}
