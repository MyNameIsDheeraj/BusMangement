<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusAttendance;
use App\Models\Student;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
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
        
        $query = BusAttendance::with(['student.user', 'bus', 'marker']);
        
        // Admin can see all attendance records
        if ($currentUser->role->name === 'admin') {
            $attendances = $query->paginate(15);
        } 
        // Teacher can see attendance for students in their classes
        elseif ($currentUser->role->name === 'teacher') {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $studentIds = Student::whereIn('class_id', $classIds)->pluck('id');
            $attendances = $query->whereIn('student_id', $studentIds)->paginate(15);
        } 
        // Driver can see attendance for their assigned bus
        elseif ($currentUser->role->name === 'driver') {
            $busIds = Bus::where('driver_id', $currentUser->id)->pluck('id');
            $attendances = $query->whereIn('bus_id', $busIds)->paginate(15);
        } 
        // Cleaner can see attendance for their assigned bus
        elseif ($currentUser->role->name === 'cleaner') {
            $busIds = Bus::where('cleaner_id', $currentUser->id)->pluck('id');
            $attendances = $query->whereIn('bus_id', $busIds)->paginate(15);
        } 
        // Parent can see attendance for their children
        elseif ($currentUser->role->name === 'parent') {
            $studentIds = $currentUser->parent->students->pluck('id');
            $attendances = $query->whereIn('student_id', $studentIds)->paginate(15);
        } 
        // Student can see their own attendance
        elseif ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if (!$student) {
                return response()->json(['error' => 'Student record not found'], 404);
            }
            $attendances = $query->where('student_id', $student->id)->get();
            return response()->json($attendances);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($attendances);
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
        
        // Admin, teacher, driver, and cleaner can mark attendance
        if (!in_array($currentUser->role->name, ['admin', 'teacher', 'driver', 'cleaner'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'bus_id' => 'required|exists:buses,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late',
            'academic_year' => 'required|regex:/^\d{4}-\d{4}$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Check if user can mark attendance for this bus
        if (!$this->canMarkAttendanceForBus($request->bus_id)) {
            return response()->json(['error' => 'Unauthorized to mark attendance for this bus'], 403);
        }

        // Add the user ID who marked the attendance
        $request['marked_by'] = $currentUser->id;
        
        $attendance = BusAttendance::create($request->all());

        return response()->json($attendance, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $attendance = BusAttendance::with(['student.user', 'bus', 'marker'])->findOrFail($id);
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if user can view this attendance record
        if (!$this->canViewAttendance($attendance)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($attendance);
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
        
        // Only admin can update attendance
        if ($currentUser->role->name !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $attendance = BusAttendance::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'student_id' => 'sometimes|required|exists:students,id',
            'bus_id' => 'sometimes|required|exists:buses,id',
            'date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:present,absent,late',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $attendance->update($request->all());

        return response()->json($attendance);
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
        
        // Only admin can delete attendance records
        if ($currentUser->role->name !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $attendance = BusAttendance::findOrFail($id);
        $attendance->delete();

        return response()->json(['message' => 'Attendance record deleted successfully']);
    }
    
    /**
     * Check if current user can mark attendance for the specified bus
     */
    private function canMarkAttendanceForBus($busId)
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return false;
        }
        
        // Admins can mark attendance for any bus
        if ($currentUser->role->name === 'admin') {
            return true;
        }
        
        // Driver can mark attendance for their assigned bus
        if ($currentUser->role->name === 'driver') {
            return Bus::where('id', $busId)->where('driver_id', $currentUser->id)->exists();
        }
        
        // Cleaner can mark attendance for their assigned bus
        if ($currentUser->role->name === 'cleaner') {
            return Bus::where('id', $busId)->where('cleaner_id', $currentUser->id)->exists();
        }
        
        return false;
    }
    
    /**
     * Check if current user can view the specified attendance record
     */
    private function canViewAttendance($attendance)
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return false;
        }
        
        // Admins can view all attendance records
        if ($currentUser->role->name === 'admin') {
            return true;
        }
        
        // Teachers can view attendance for students in their classes
        if ($currentUser->role->name === 'teacher') {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $studentIds = Student::whereIn('class_id', $classIds)->pluck('id');
            return $studentIds->contains($attendance->student_id);
        }
        
        // Driver can view attendance for their assigned bus
        if ($currentUser->role->name === 'driver') {
            return $attendance->bus_id == \App\Models\Bus::where('driver_id', $currentUser->id)->pluck('id')->first();
        }
        
        // Cleaner can view attendance for their assigned bus
        if ($currentUser->role->name === 'cleaner') {
            return $attendance->bus_id == \App\Models\Bus::where('cleaner_id', $currentUser->id)->pluck('id')->first();
        }
        
        // Parents can view attendance for their children
        if ($currentUser->role->name === 'parent') {
            $studentIds = $currentUser->parent->students->pluck('id');
            return $studentIds->contains($attendance->student_id);
        }
        
        // Students can view their own attendance
        if ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if (!$student) {
                return false;
            }
            return $student->id === $attendance->student_id;
        }
        
        return false;
    }
}
