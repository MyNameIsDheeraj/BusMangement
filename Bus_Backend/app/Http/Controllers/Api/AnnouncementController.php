<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
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
        
        $query = Announcement::with('creator');
        
        // Filter announcements based on audience
        switch ($currentUser->role->name) {
            case 'admin':
                // Admins can see all announcements
                $announcements = $query->paginate(15);
                break;
            case 'teacher':
                // Teachers can see announcements for all, teachers, or admin
                $announcements = $query->whereIn('audience', ['all', 'teachers', 'admin'])->paginate(15);
                break;
            case 'parent':
                // Parents can see announcements for all or parents
                $announcements = $query->whereIn('audience', ['all', 'parents'])->paginate(15);
                break;
            case 'student':
                // Students can see announcements for all or students
                $announcements = $query->whereIn('audience', ['all', 'students'])->paginate(15);
                break;
            case 'driver':
            case 'cleaner':
                // Drivers and cleaners can see announcements for all or staff
                $announcements = $query->whereIn('audience', ['all'])->paginate(15);
                break;
            default:
                return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($announcements);
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
        
        // Only admin can create announcements
        if ($currentUser->role->name !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'audience' => 'required|in:all,students,parents,teachers,admin',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Add the creating user ID
        $request['created_by'] = $currentUser->id;
        
        $announcement = Announcement::create($request->all());

        return response()->json($announcement, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $announcement = Announcement::with('creator')->findOrFail($id);
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if user can view this announcement based on audience
        if (!$this->canViewAnnouncement($announcement)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($announcement);
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
        
        // Only admin can update announcements
        if ($currentUser->role->name !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $announcement = Announcement::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'audience' => 'sometimes|required|in:all,students,parents,teachers,admin',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $announcement->update($request->all());

        return response()->json($announcement);
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
        
        // Only admin can delete announcements
        if ($currentUser->role->name !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted successfully']);
    }
    
    /**
     * Check if current user can view the specified announcement
     */
    private function canViewAnnouncement($announcement)
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return false;
        }
        
        // Admins can view all announcements
        if ($currentUser->role->name === 'admin') {
            return true;
        }
        
        // Check audience
        switch ($announcement->audience) {
            case 'all':
                return true;
            case 'students':
                return $currentUser->role->name === 'student';
            case 'parents':
                return $currentUser->role->name === 'parent';
            case 'teachers':
                return $currentUser->role->name === 'teacher';
            case 'admin':
                return $currentUser->role->name === 'admin';
            default:
                return false;
        }
    }
}
