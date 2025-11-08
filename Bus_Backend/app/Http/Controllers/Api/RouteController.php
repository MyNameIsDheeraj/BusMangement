<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RouteRequest;
use App\Http\Resources\RouteResource;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\ErrorResource;
use App\Models\Route;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentUser = auth('api')->user();
        
        $query = Route::with(['bus', 'stops']);
        
        // Admin can see all routes
        if ($currentUser->role->name === 'admin') {
            $routes = $query->paginate(15);
            return new SuccessResource($routes->through(function ($route) {
                return new RouteResource($route);
            }));
        } 
        // Driver can only see routes for their assigned bus
        elseif ($currentUser->role->name === 'driver') {
            $busIds = Bus::where('driver_id', $currentUser->id)->pluck('id');
            $routes = $query->whereIn('bus_id', $busIds)->paginate(15);
            return new SuccessResource($routes->through(function ($route) {
                return new RouteResource($route);
            }));
        } 
        // Cleaner can only see routes for their assigned bus
        elseif ($currentUser->role->name === 'cleaner') {
            $busIds = Bus::where('cleaner_id', $currentUser->id)->pluck('id');
            $routes = $query->whereIn('bus_id', $busIds)->paginate(15);
            return new SuccessResource($routes->through(function ($route) {
                return new RouteResource($route);
            }));
        } 
        // Teacher can see routes related to their classes
        elseif ($currentUser->role->name === 'teacher') {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $studentIds = \App\Models\Student::whereIn('class_id', $classIds)->pluck('id');
            $stopIds = \App\Models\StudentRoute::whereIn('student_id', $studentIds)->pluck('stop_id');
            $routeIds = \App\Models\Stop::whereIn('id', $stopIds)->pluck('route_id');
            $routes = $query->whereIn('id', $routeIds)->paginate(15);
            return new SuccessResource($routes->through(function ($route) {
                return new RouteResource($route);
            }));
        } 
        // Parent can see routes for their children
        elseif ($currentUser->role->name === 'parent') {
            $studentIds = $currentUser->parent->students->pluck('id');
            $stopIds = \App\Models\StudentRoute::whereIn('student_id', $studentIds)->pluck('stop_id');
            $routeIds = \App\Models\Stop::whereIn('id', $stopIds)->pluck('route_id');
            $routes = $query->whereIn('id', $routeIds)->paginate(15);
            return new SuccessResource($routes->through(function ($route) {
                return new RouteResource($route);
            }));
        } 
        // Student can see their route
        elseif ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if (!$student) {
                return new ErrorResource(['message' => 'Student record not found'], 404);
            }
            $stopIds = [$student->pickup_stop_id, $student->drop_stop_id];
            $routes = $query->whereIn('id', \App\Models\Stop::whereIn('id', $stopIds)->pluck('route_id'))->get();
            return new SuccessResource($routes->map(function ($route) {
                return new RouteResource($route);
            }));
        } else {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RouteRequest $request)
    {
        $currentUser = auth('api')->user();
        
        // Only admin can create routes
        if ($currentUser->role->name !== 'admin') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }

        $route = Route::create($request->validated());

        return new SuccessResource(new RouteResource($route), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $route = Route::with(['bus', 'stops'])->findOrFail($id);
        $currentUser = auth('api')->user();
        
        // Check if user can view this route
        if (!$this->canViewRoute($route)) {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        return new SuccessResource(new RouteResource($route));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RouteRequest $request, $id)
    {
        $currentUser = auth('api')->user();
        
        // Only admin can update routes
        if ($currentUser->role->name !== 'admin') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $route = Route::findOrFail($id);

        $route->update($request->validated());

        return new SuccessResource(new RouteResource($route));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $currentUser = auth('api')->user();
        
        // Only admin can delete routes
        if ($currentUser->role->name !== 'admin') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $route = Route::findOrFail($id);
        $route->delete();

        return new SuccessResource(null, ['message' => 'Route deleted successfully']);
    }
    
    /**
     * Check if current user can view the specified route
     */
    private function canViewRoute($route)
    {
        $currentUser = auth('api')->user();
        
        // Admins can view all routes
        if ($currentUser->role->name === 'admin') {
            return true;
        }
        
        // Driver can view routes for their assigned bus
        if ($currentUser->role->name === 'driver') {
            $busIds = Bus::where('driver_id', $currentUser->id)->pluck('id');
            return $busIds->contains($route->bus_id);
        }
        
        // Cleaner can view routes for their assigned bus
        if ($currentUser->role->name === 'cleaner') {
            $busIds = Bus::where('cleaner_id', $currentUser->id)->pluck('id');
            return $busIds->contains($route->bus_id);
        }
        
        // Teacher can view routes related to their classes
        if ($currentUser->role->name === 'teacher') {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $studentIds = \App\Models\Student::whereIn('class_id', $classIds)->pluck('id');
            $stopIds = \App\Models\StudentRoute::whereIn('student_id', $studentIds)->pluck('stop_id');
            $routeIds = \App\Models\Stop::whereIn('id', $stopIds)->pluck('route_id');
            return $routeIds->contains($route->id);
        }
        
        // Parent can view routes for their children
        if ($currentUser->role->name === 'parent') {
            $studentIds = $currentUser->parent->students->pluck('id');
            $stopIds = \App\Models\StudentRoute::whereIn('student_id', $studentIds)->pluck('stop_id');
            $routeIds = \App\Models\Stop::whereIn('id', $stopIds)->pluck('route_id');
            return $routeIds->contains($route->id);
        }
        
        // Student can view their route
        if ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if (!$student) {
                return false;
            }
            $stopIds = [$student->pickup_stop_id, $student->drop_stop_id];
            $routeIds = \App\Models\Stop::whereIn('id', $stopIds)->pluck('route_id');
            return $routeIds->contains($route->id);
        }
        
        return false;
    }
}
