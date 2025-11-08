<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        // Get the authenticated user
        $user = auth('api')->user();
        
        // Check if user is authenticated
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        // Check if user has any of the required permissions
        $userPermissions = $user->role->permissions->pluck('name')->toArray();
        
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions)) {
                $hasPermission = true;
                break;
            }
        }
        
        if (!$hasPermission) {
            return response()->json(['error' => 'User does not have the required permission(s)'], 403);
        }

        return $next($request);
    }
}