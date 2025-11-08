<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class AuthController extends Controller
{
    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        $user = auth('api')->user();
        $role = $user->role ? $user->role->name : null;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user,
            'role' => $role
        ]);
    }

    /**
     * Register a new user (admin only).
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Check if the authenticated user is an admin
        $currentUser = auth('api')->user();
        if (!$currentUser || !$currentUser->role || $currentUser->role->name !== 'admin') {
            return response()->json(['error' => 'Unauthorized. Only admin users can register new users.'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'role_id' => $request->get('role_id'),
        ]);

        // For admin registration, don't return token for the created user
        // Instead return success message and user data
        $role = $user->role ? $user->role->name : null;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role
            ]
        ], 201);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     * This endpoint works even if the token is expired, as long as it's within the refresh_ttl window.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        try {
            $token = null;
            
            // Try to parse token from Authorization header
            // This will work even if the token is expired (within refresh_ttl)
            try {
                $token = JWTAuth::parseToken()->getToken();
            } catch (TokenExpiredException $e) {
                // Token is expired, but we can still refresh if within refresh_ttl
                // Get the token from the exception - this is the expired token we want to refresh
                $token = $e->getToken();
            } catch (\Exception $e) {
                // Token is missing or cannot be parsed
                return response()->json([
                    'error' => 'Token not provided or invalid',
                    'message' => 'Please provide a valid token in the Authorization header'
                ], 401);
            }
            
            // Refresh the token (works even if expired, as long as within refresh_ttl)
            // The refresh method handles expired tokens automatically
            $newToken = JWTAuth::refresh($token);
            
            // Get the user from the refreshed token
            $user = JWTAuth::setToken($newToken)->authenticate();
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            
            $role = $user->role ? $user->role->name : null;
            
            return response()->json([
                'access_token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => $user,
                'role' => $role
            ]);
        } catch (TokenExpiredException $e) {
            // Token is expired beyond refresh window (beyond refresh_ttl)
            return response()->json([
                'error' => 'Token refresh failed',
                'message' => 'The token has expired beyond the refresh window. Please login again.'
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'error' => 'Invalid token',
                'message' => 'The provided token is invalid'
            ], 401);
        } catch (TokenBlacklistedException $e) {
            return response()->json([
                'error' => 'Token has been blacklisted',
                'message' => 'This token has been invalidated. Please login again.'
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Could not refresh token',
                'message' => $e->getMessage()
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Token refresh failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
