<?php

use Illuminate\Support\Facades\Route;

// API Documentation
Route::get('/', function () {
    return view('api.documentation');
});

// Keep the Swagger UI route for interactive documentation
Route::get('/api/documentation', function () {
    return view('api.documentation');
});

// Fallback login route to prevent RouteNotFoundException
// This is needed for web authentication redirects
Route::get('login', function () {
    return redirect('/'); // Redirect to home since this is API-focused
})->name('login');

Route::post('login', function () {
    return response()->json(['error' => 'Use API authentication instead'], 401);
})->name('login.post');
