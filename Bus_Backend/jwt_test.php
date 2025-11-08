#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Create a kernel instance
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test JWT functionality
try {
    echo "=== JWT AUTHENTICATION TEST ===\n";
    
    // Test 1: Check if JWT package is properly loaded
    echo "1. Testing JWT package availability...\n";
    if (class_exists('Tymon\JWTAuth\Facades\JWTAuth')) {
        echo "   ✓ JWTAuth facade is available\n";
    } else {
        echo "   ✗ JWTAuth facade is NOT available\n";
    }
    
    // Test 2: Check JWT configuration
    echo "2. Testing JWT configuration...\n";
    $jwtSecret = config('jwt.secret');
    if ($jwtSecret) {
        echo "   ✓ JWT secret is configured (length: " . strlen($jwtSecret) . " characters)\n";
    } else {
        echo "   ✗ JWT secret is NOT configured\n";
    }
    
    // Test 3: Test JWT token generation
    echo "3. Testing JWT token generation...\n";
    try {
        // Create a simple user array for testing
        $testUser = [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com'
        ];
        
        // Generate a token
        $token = app('tymon.jwt.auth')->fromUser((object)$testUser);
        if ($token) {
            echo "   ✓ JWT token generated successfully (length: " . strlen($token) . " characters)\n";
        } else {
            echo "   ✗ Failed to generate JWT token\n";
        }
    } catch (Exception $e) {
        echo "   ✗ Error generating JWT token: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Check auth configuration
    echo "4. Testing auth configuration...\n";
    $guards = config('auth.guards');
    if (isset($guards['api']) && $guards['api']['driver'] === 'jwt') {
        echo "   ✓ API guard is configured to use JWT driver\n";
    } else {
        echo "   ✗ API guard is NOT configured to use JWT driver\n";
        print_r($guards);
    }
    
    echo "\n=== JWT TEST COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "Error during JWT test: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}