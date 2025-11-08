<?php

// Simple test script to verify authentication endpoints
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Configuration
$baseUrl = 'http://localhost:8000/api/v1'; // Assuming Laravel is running on port 8000
$testEmail = 'admin@example.com'; // Default test admin user
$testPassword = 'password'; // Default test password

echo "Testing Authentication Endpoints\n";
echo "===============================\n";

// Test 1: Login to get token
echo "1. Testing Login endpoint...\n";
$loginResponse = Http::post($baseUrl . '/login', [
    'email' => $testEmail,
    'password' => $testPassword
]);

if ($loginResponse->successful()) {
    $loginData = $loginResponse->json();
    $token = $loginData['access_token'] ?? null;
    echo "   ✓ Login successful\n";
    echo "   Token obtained: " . ($token ? "Yes" : "No") . "\n";
} else {
    echo "   ✗ Login failed: " . $loginResponse->body() . "\n";
    exit(1);
}

// Test 2: Test refresh endpoint
if ($token) {
    echo "\n2. Testing Refresh endpoint...\n";
    $refreshResponse = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->post($baseUrl . '/refresh');

    if ($refreshResponse->successful()) {
        $refreshData = $refreshResponse->json();
        $newToken = $refreshData['access_token'] ?? null;
        echo "   ✓ Refresh successful\n";
        echo "   New token obtained: " . ($newToken ? "Yes" : "No") . "\n";
        $token = $newToken; // Use new token for subsequent requests
    } else {
        echo "   ✗ Refresh failed: " . $refreshResponse->body() . "\n";
    }
}

// Test 3: Test logout endpoint
if ($token) {
    echo "\n3. Testing Logout endpoint...\n";
    $logoutResponse = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->post($baseUrl . '/logout');

    if ($logoutResponse->successful()) {
        echo "   ✓ Logout successful\n";
    } else {
        echo "   ✗ Logout failed: " . $logoutResponse->body() . "\n";
    }
}

// Test 4: Try to access protected endpoint after logout
if ($token) {
    echo "\n4. Testing access to protected endpoint after logout...\n";
    $protectedResponse = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->get($baseUrl . '/me');

    if ($protectedResponse->status() === 401) {
        echo "   ✓ Token properly invalidated after logout\n";
    } else {
        echo "   ✗ Token still valid after logout\n";
    }
}

echo "\nAuthentication endpoints test completed.\n";