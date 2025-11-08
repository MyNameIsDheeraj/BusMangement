<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Create a kernel instance
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== AUTHENTICATION SYSTEM TEST ===\n";

// Test if we can create a user and authenticate them
try {
    use App\Models\User;
    use Illuminate\Support\Facades\Hash;
    
    // Create a test user if one doesn't exist
    $user = User::firstOrCreate(
        ['email' => 'test@example.com'],
        [
            'name' => 'Test User',
            'password' => Hash::make('password'),
            'role_id' => 1,
        ]
    );
    
    echo "1. Test user created/located: " . $user->name . " (" . $user->email . ")\n";
    
    // Test JWT authentication
    $credentials = ['email' => 'test@example.com', 'password' => 'password'];
    
    if (auth('api')->attempt($credentials)) {
        echo "2. ✓ Authentication successful\n";
        
        // Get the authenticated user
        $authenticatedUser = auth('api')->user();
        echo "3. Authenticated user: " . $authenticatedUser->name . "\n";
        
        // Generate a token
        $token = auth('api')->login($authenticatedUser);
        echo "4. JWT token generated (length: " . strlen($token) . " characters)\n";
        
        // Test token refresh
        $refreshedToken = auth('api')->refresh();
        echo "5. ✓ Token refresh successful\n";
        
        // Test logout
        auth('api')->logout();
        echo "6. ✓ Logout successful\n";
    } else {
        echo "2. ✗ Authentication failed\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== AUTHENTICATION TEST COMPLETE ===\n";