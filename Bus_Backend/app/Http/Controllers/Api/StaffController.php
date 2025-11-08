<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffRequest;
use App\Http\Resources\StaffResource;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\ErrorResource;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class StaffController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/staff-profiles",
     *     tags={"Staff"},
     *     summary="Get list of staff profiles",
     *     description="Returns a paginated list of staff profiles based on user role",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(ref="#/components/schemas/StaffProfile")
     *                 ),
     *                 @OA\Property(property="links", type="object"),
     *                 @OA\Property(property="meta", type="object")
     *             ),
     *             @OA\Property(property="message", type="string", example=null),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="timestamp", type="string", example="2025-11-04T02:30:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    /**
     * Display all staff profiles with salary information.
     */
    public function index()
    {
        $currentUser = auth('api')->user();
        
        // Check if user has permission to view staff profiles
        if (!$this->hasPermission($currentUser, 'view_staff')) {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $query = StaffProfile::with(['user', 'user.role', 'bus']);
        
        // Admin can view all staff profiles
        if ($currentUser->role->name === 'admin') {
            $staffProfiles = $query->paginate(15);
        } 
        // Driver/Cleaner can view their own profile only
        elseif ($currentUser->role->name === 'driver' || $currentUser->role->name === 'cleaner') {
            $staffProfiles = $query->where('user_id', $currentUser->id)->paginate(15);
        } 
        else {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        return new SuccessResource($staffProfiles->through(function ($staff) {
            return new StaffResource($staff);
        }));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/staff-profiles/{id}",
     *     tags={"Staff"},
     *     summary="Get specific staff profile",
     *     description="Returns a specific staff profile",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Staff profile ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/StaffProfile"),
     *             @OA\Property(property="message", type="string", example=null),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="timestamp", type="string", example="2025-11-04T02:30:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Staff profile not found"
     *     )
     * )
     */
    /**
     * Display a specific staff profile with salary information.
     */
    public function show($id)
    {
        $currentUser = auth('api')->user();
        
        $staffProfile = StaffProfile::with(['user', 'user.role', 'bus'])->findOrFail($id);
        
        // Admin can view any staff profile
        if ($currentUser->role->name === 'admin') {
            return new SuccessResource(new StaffResource($staffProfile));
        }
        
        // Staff can only view their own profile
        if ($currentUser->id === $staffProfile->user_id) {
            return new SuccessResource(new StaffResource($staffProfile));
        }
        
        // Driver/cleaner can view their own profile
        if (($currentUser->role->name === 'driver' || $currentUser->role->name === 'cleaner') && $currentUser->id === $staffProfile->user_id) {
            return new SuccessResource(new StaffResource($staffProfile));
        }
        
        return new ErrorResource(['message' => 'Unauthorized'], 403);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/staff-profiles",
     *     tags={"Staff"},
     *     summary="Create a new staff profile",
     *     description="Creates a new staff profile",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "salary"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="salary", type="number", format="decimal", example=3000.00),
     *             @OA\Property(property="license_number", type="string", example="DL123456"),
     *             @OA\Property(property="emergency_contact", type="string", example="John Doe - 1234567890"),
     *             @OA\Property(property="bus_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Staff profile created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/StaffProfile"),
     *             @OA\Property(property="message", type="string", example=null),
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(property="timestamp", type="string", example="2025-11-04T02:30:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    /**
     * Store a new staff profile (admin only).
     */
    public function store(StaffRequest $request)
    {
        $currentUser = auth('api')->user();
        
        if ($currentUser->role->name !== 'admin') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }

        $staffProfile = StaffProfile::create($request->validated());
        $staffProfile->load(['user', 'user.role', 'bus']);

        return new SuccessResource(new StaffResource($staffProfile), 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/staff-profiles/{id}",
     *     tags={"Staff"},
     *     summary="Update a staff profile",
     *     description="Updates a staff profile",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Staff profile ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="salary", type="number", format="decimal", example=3200.00),
     *             @OA\Property(property="license_number", type="string", example="DL123456"),
     *             @OA\Property(property="emergency_contact", type="string", example="John Doe - 1234567890"),
     *             @OA\Property(property="bus_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Staff profile updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/StaffProfile"),
     *             @OA\Property(property="message", type="string", example=null),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="timestamp", type="string", example="2025-11-04T02:30:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Staff profile not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    /**
     * Update an existing staff profile (specific permissions).
     */
    public function update(StaffRequest $request, $id)
    {
        $currentUser = auth('api')->user();
        
        $staffProfile = StaffProfile::findOrFail($id);
        
        // Admin can update any staff profile
        if ($currentUser->role->name === 'admin') {
            $staffProfile->update($request->validated());
            $staffProfile->load(['user', 'user.role', 'bus']);

            return new SuccessResource(new StaffResource($staffProfile));
        }
        
        // Staff can update only their own profile (excluding salary for non-admins)
        if ($currentUser->role->name === 'driver' || $currentUser->role->name === 'cleaner') {
            if ($currentUser->id === $staffProfile->user_id) {
                // Remove salary from update data for non-admin staff
                $updateData = $request->validated();
                unset($updateData['salary']); // Prevent salary modification by non-admins

                $staffProfile->update($updateData);
                $staffProfile->load(['user', 'user.role', 'bus']);

                return new SuccessResource(new StaffResource($staffProfile));
            }
        }
        
        return new ErrorResource(['message' => 'Unauthorized'], 403);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/staff-profiles/{id}",
     *     tags={"Staff"},
     *     summary="Delete a staff profile",
     *     description="Deletes a staff profile",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Staff profile ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Staff profile deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Staff profile deleted successfully"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="timestamp", type="string", example="2025-11-04T02:30:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Staff profile not found"
     *     )
     * )
     */
    /**
     * Delete a staff profile (admin only).
     */
    public function destroy($id)
    {
        $currentUser = auth('api')->user();
        
        if ($currentUser->role->name !== 'admin') {
            return new ErrorResource(['message' => 'Unauthorized'], 403);
        }
        
        $staffProfile = StaffProfile::findOrFail($id);
        $staffProfile->delete();

        return new SuccessResource(null, ['message' => 'Staff profile deleted successfully']);
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