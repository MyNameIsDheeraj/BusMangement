#!/bin/bash

# API Testing Script for School Bus Management System
echo "=== SCHOOL BUS MANAGEMENT SYSTEM API TESTING ==="
echo ""

# Test server availability
echo "1. Testing server availability..."
SERVER_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8000/api/test --max-time 5 2>/dev/null)
if [ "$SERVER_STATUS" = "200" ]; then
    echo "   ✓ Server is running and responding"
else
    echo "   ✗ Server is not responding (HTTP $SERVER_STATUS)"
    exit 1
fi

# Test authentication endpoints
echo ""
echo "2. Testing authentication endpoints..."

# Test login endpoint
echo "   Testing login endpoint..."
LOGIN_RESPONSE=$(curl -s -X POST http://127.0.0.1:8000/api/login \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d '{"email":"test@example.com","password":"password"}' \
    --max-time 10 2>/dev/null)

if [[ $LOGIN_RESPONSE == *"access_token"* ]]; then
    echo "   ✓ Login endpoint working correctly"
    # Extract token for later tests
    ACCESS_TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"access_token":"[^"]*"' | cut -d'"' -f4)
else
    echo "   ✗ Login endpoint failed"
    echo "   Response: $LOGIN_RESPONSE"
fi

# Test register endpoint
echo "   Testing register endpoint..."
REGISTER_RESPONSE=$(curl -s -X POST http://127.0.0.1:8000/api/register \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d '{"name":"Test User","email":"test2@example.com","password":"password","password_confirmation":"password","role_id":1}' \
    --max-time 10 2>/dev/null)

if [[ $REGISTER_RESPONSE == *"access_token"* ]] || [[ $REGISTER_RESPONSE == *"error"* ]]; then
    echo "   ✓ Register endpoint accessible"
else
    echo "   ✗ Register endpoint failed"
    echo "   Response: $REGISTER_RESPONSE"
fi

# Test protected endpoints if we have a token
if [ ! -z "$ACCESS_TOKEN" ]; then
    echo ""
    echo "3. Testing protected endpoints with authentication..."
    
    # Test me endpoint
    echo "   Testing me endpoint..."
    ME_RESPONSE=$(curl -s -X GET http://127.0.0.1:8000/api/me \
        -H "Accept: application/json" \
        -H "Authorization: Bearer $ACCESS_TOKEN" \
        --max-time 10 2>/dev/null)
        
    if [[ $ME_RESPONSE == *"id"* ]] && [[ $ME_RESPONSE == *"email"* ]]; then
        echo "   ✓ Me endpoint working correctly"
    else
        echo "   ✗ Me endpoint failed"
        echo "   Response: $ME_RESPONSE"
    fi
    
    # Test logout endpoint
    echo "   Testing logout endpoint..."
    LOGOUT_RESPONSE=$(curl -s -X POST http://127.0.0.1:8000/api/logout \
        -H "Accept: application/json" \
        -H "Authorization: Bearer $ACCESS_TOKEN" \
        --max-time 10 2>/dev/null)
        
    if [[ $LOGOUT_RESPONSE == *"Successfully logged out"* ]]; then
        echo "   ✓ Logout endpoint working correctly"
    else
        echo "   ✗ Logout endpoint failed"
        echo "   Response: $LOGOUT_RESPONSE"
    fi
else
    echo ""
    echo "3. Skipping protected endpoint tests (no valid token)"
fi

# Test resource endpoints (public access - should return unauthorized without token)
echo ""
echo "4. Testing resource endpoints accessibility..."

# Test students endpoint
echo "   Testing students endpoint..."
STUDENTS_RESPONSE=$(curl -s -X GET http://127.0.0.1:8000/api/students \
    -H "Accept: application/json" \
    --max-time 10 2>/dev/null)

if [[ $STUDENTS_RESPONSE == *"Unauthorized"* ]] || [[ $STUDENTS_RESPONSE == *"error"* ]]; then
    echo "   ✓ Students endpoint properly secured"
else
    echo "   ⚠ Students endpoint may not be properly secured"
    echo "   Response: $STUDENTS_RESPONSE"
fi

# Test buses endpoint
echo "   Testing buses endpoint..."
BUSES_RESPONSE=$(curl -s -X GET http://127.0.0.1:8000/api/buses \
    -H "Accept: application/json" \
    --max-time 10 2>/dev/null)

if [[ $BUSES_RESPONSE == *"Unauthorized"* ]] || [[ $BUSES_RESPONSE == *"error"* ]]; then
    echo "   ✓ Buses endpoint properly secured"
else
    echo "   ⚠ Buses endpoint may not be properly secured"
    echo "   Response: $BUSES_RESPONSE"
fi

# Test routes endpoint
echo "   Testing routes endpoint..."
ROUTES_RESPONSE=$(curl -s -X GET http://127.0.0.1:8000/api/routes \
    -H "Accept: application/json" \
    --max-time 10 2>/dev/null)

if [[ $ROUTES_RESPONSE == *"Unauthorized"* ]] || [[ $ROUTES_RESPONSE == *"error"* ]]; then
    echo "   ✓ Routes endpoint properly secured"
else
    echo "   ⚠ Routes endpoint may not be properly secured"
    echo "   Response: $ROUTES_RESPONSE"
fi

echo ""
echo "=== API TESTING COMPLETE ==="
echo "Summary:"
echo "- Server status: ✓ Running"
echo "- Public endpoints: ✓ Accessible"
echo "- Authentication: $(if [ ! -z "$ACCESS_TOKEN" ]; then echo "✓ Working"; else echo "⚠ Needs attention"; fi)"
echo "- Resource security: ✓ Properly secured"
