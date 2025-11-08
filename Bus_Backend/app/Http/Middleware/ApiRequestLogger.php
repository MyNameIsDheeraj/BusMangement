<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiRequestLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log the incoming API request
        Log::info('API Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => $request->headers->all(),
            'query_params' => $request->query(),
            'body' => $request->except(['password', 'password_confirmation']), // Exclude sensitive data
            'user_id' => auth('api')->id() ?? null,
            'timestamp' => now()->toISOString(),
        ]);

        $response = $next($request);

        // Log the response
        Log::info('API Response', [
            'url' => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'response_time' => microtime(true) - LARAVEL_START,
            'user_id' => auth('api')->id() ?? null,
            'timestamp' => now()->toISOString(),
        ]);

        return $response;
    }
}
