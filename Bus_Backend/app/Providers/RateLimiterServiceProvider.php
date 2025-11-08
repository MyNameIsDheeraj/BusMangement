<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class RateLimiterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(
                $request->user()?->id ?: $request->ip()
            );
        });
        
        // Rate limiter for authenticated users
        RateLimiter::for('api-authenticated', function (Request $request) {
            if ($request->user()) {
                // Different limits based on user role
                $role = $request->user()->role->name ?? 'default';
                
                $limits = [
                    'admin' => 300,
                    'teacher' => 120,
                    'parent' => 100,
                    'student' => 60,
                    'driver' => 100,
                    'cleaner' => 100,
                ];
                
                $maxAttempts = $limits[$role] ?? 60;
                
                return Limit::perMinute($maxAttempts)->by($request->user()->id);
            }
            
            return Limit::perMinute(30)->by($request->ip());
        });
    }
}
