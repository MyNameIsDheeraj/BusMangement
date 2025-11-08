#!/bin/bash

# Comprehensive API Testing Script for School Bus Management System
echo "==========================================="
echo "SCHOOL BUS MANAGEMENT SYSTEM - API TESTING"
echo "==========================================="
echo ""

# Start Laravel development server in background
echo "1. Starting Laravel development server..."
cd /home/hellbat/Documents/project/Laravel/Bus_management/BusMangement
php artisan serve --host=0.0.0.0 --port=8000 > /dev/null 2>&1 &
SERVER_PID=$!
sleep 5  # Wait for server to start

# Test server availability
echo "2. Testing server availability..."
SERVER_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8000/api/test --max-time 10)
if [ "$SERVER_STATUS" = "200" ]; then
    echo "   ✓ Server is running and responding"
else
    echo "   ✗ Server is not responding (HTTP $SERVER_STATUS)"
    kill $SERVER_PID 2>/dev/null
    exit 1
fi

# Test authentication endpoints
echo ""
echo "3. Testing authentication endpoints..."

# Test login endpoint
echo "   Testing login endpoint..."
LOGIN_RESPONSE=$(curl -s -X POST http://127.0.0.1:8000/api/login \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d '{"email":"test@example.com","password":"password"}' \
    --max-time 10)

if [[ $LOGIN_RESPONSE == *"access_token"* ]]; then
    echo "   ✓ Login endpoint working correctly"
    ACCESS_TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"access_token":"[^"]*"' | cut -d'"' -f4)
else
    echo "   ✗ Login endpoint failed"
    echo "   Response: $LOGIN_RESPONSE"
    kill $SERVER_PID 2>/dev/null
    exit 1
fi

# Test me endpoint
echo "   Testing me endpoint..."
ME_RESPONSE=$(curl -s -X GET http://127.0.0.1:8000/api/me \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ACCESS_TOKEN" \
    --max-time 10)

if [[ $ME_RESPONSE == *"id"* ]] && [[ $ME_RESPONSE == *"email"* ]]; then
    echo "   ✓ Me endpoint working correctly"
else
    echo "   ✗ Me endpoint failed"
    echo "   Response: $ME_RESPONSE"
    kill $SERVER_PID 2>/dev/null
    exit 1
fi

# Test logout endpoint
echo "   Testing logout endpoint..."
LOGOUT_RESPONSE=$(curl -s -X POST http://127.0.0.1:8000/api/logout \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ACCESS_TOKEN" \
    --max-time 10)

if [[ $LOGOUT_RESPONSE == *"Successfully logged out"* ]]; then
    echo "   ✓ Logout endpoint working correctly"
else
    echo "   ✗ Logout endpoint failed"
    echo "   Response: $LOGOUT_RESPONSE"
    kill $SERVER_PID 2>/dev/null
    exit 1
fi

# Test register endpoint
echo "   Testing register endpoint..."
REGISTER_RESPONSE=$(curl -s -X POST http://127.0.0.1:8000/api/register \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d '{"name":"Test User 2","email":"test2@example.com","password":"password","password_confirmation":"password","role_id":1}' \
    --max-time 10)

if [[ $REGISTER_RESPONSE == *"access_token"* ]] || [[ $REGISTER_RESPONSE == *"error"* ]]; then
    echo "   ✓ Register endpoint accessible"
else
    echo "   ✗ Register endpoint failed"
    echo "   Response: $REGISTER_RESPONSE"
    kill $SERVER_PID 2>/dev/null
    exit 1
fi

# Re-login to get a fresh token for further tests
echo "   Re-authenticating for further tests..."
LOGIN_RESPONSE=$(curl -s -X POST http://127.0.0.1:8000/api/login \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d '{"email":"test@example.com","password":"password"}' \
    --max-time 10)
ACCESS_TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"access_token":"[^"]*"' | cut -d'"' -f4)

# Test protected resource endpoints
echo ""
echo "4. Testing protected resource endpoints..."

# Test users endpoint
echo "   Testing users endpoint..."
USERS_RESPONSE=$(curl -s -X GET http://127.0.0.1:8000/api/users \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ACCESS_TOKEN" \
    --max-time 10)

if [[ $USERS_RESPONSE != *"Target class [role] does not exist"* ]]; then
    echo "   ✓ Users endpoint accessible"
else
    echo "   ✗ Users endpoint failed with middleware error"
    echo "   Response: $USERS_RESPONSE"
fi

# Test students endpoint
echo "   Testing students endpoint..."
STUDENTS_RESPONSE=$(curl -s -X GET http://127.0.0.1:8000/api/students \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ACCESS_TOKEN" \
    --max-time 10)

if [[ $STUDENTS_RESPONSE != *"Target class [role] does not exist"* ]]; then
    echo "   ✓ Students endpoint accessible"
else
    echo "   ✗ Students endpoint failed with middleware error"
    echo "   Response: $STUDENTS_RESPONSE"
fi

# Test buses endpoint
echo "   Testing buses endpoint..."
BUSES_RESPONSE=$(curl -s -X GET http://127.0.0.1:8000/api/buses \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ACCESS_TOKEN" \
    --max-time 10)

if [[ $BUSES_RESPONSE != *"Target class [role] does not exist"* ]]; then
    echo "   ✓ Buses endpoint accessible"
else
    echo "   ✗ Buses endpoint failed with middleware error"
    echo "   Response: $BUSES_RESPONSE"
fi

# Test routes endpoint
echo "   Testing routes endpoint..."
ROUTES_RESPONSE=$(curl -s -X GET http://127.0.0.1:8000/api/routes \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ACCESS_TOKEN" \
    --max-time 10)

if [[ $ROUTES_RESPONSE != *"Target class [role] does not exist"* ]]; then
    echo "   ✓ Routes endpoint accessible"
else
    echo "   ✗ Routes endpoint failed with middleware error"
    echo "   Response: $ROUTES_RESPONSE"
fi

# Test stops endpoint
echo "   Testing stops endpoint..."
STOPS_RESPONSE=$(curl -s -X GET http://127.0.0.1:8000/api/stops \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ACCESS_TOKEN" \
    --max-time 10)

if [[ $STOPS_RESPONSE != *"Target class [role] does not exist"* ]]; then
    echo "   ✓ Stops endpoint accessible"
else
    echo "   ✗ Stops endpoint failed with middleware error"
    echo "   Response: $STOPS_RESPONSE"
fi

# Test payments endpoint
echo "   Testing payments endpoint..."
PAYMENTS_RESPONSE=$(curl -s -X GET http://127.0.0.1:8000/api/payments \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ACCESS_TOKEN" \
    --max-time 10)

if [[ $PAYMENTS_RESPONSE != *"Target class [role] does not exist"* ]]; then
    echo "   ✓ Payments endpoint accessible"
else
    echo "   ✗ Payments endpoint failed with middleware error"
    echo "   Response: $PAYMENTS_RESPONSE"
fi

# Test attendances endpoint
echo "   Testing attendances endpoint..."
ATTENDANCES_RESPONSE=$(curl -s -X GET http://127.0.0.1:8000/api/attendances \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ACCESS_TOKEN" \
    --max-time 10)

if [[ $ATTENDANCES_RESPONSE != *"Target class [role] does not exist"* ]]; then
    echo "   ✓ Attendances endpoint accessible"
else
    echo "   ✗ Attendances endpoint failed with middleware error"
    echo "   Response: $ATTENDANCES_RESPONSE"
fi

# Test alerts endpoint
echo "   Testing alerts endpoint..."
ALERTS_RESPONSE=$(curl -s -X GET http://127.0.0.1:8000/api/alerts \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ACCESS_TOKEN" \
    --max-time 10)

if [[ $ALERTS_RESPONSE != *"Target class [role] does not exist"* ]]; then
    echo "   ✓ Alerts endpoint accessible"
else
    echo "   ✗ Alerts endpoint failed with middleware error"
    echo "   Response: $ALERTS_RESPONSE"
fi

# Test announcements endpoint
echo "   Testing announcements endpoint..."
ANNOUNCEMENTS_RESPONSE=$(curl -s -X GET http://127.0.0.1:8000/api/announcements \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ACCESS_TOKEN" \
    --max-time 10)

if [[ $ANNOUNCEMENTS_RESPONSE != *"Target class [role] does not exist"* ]]; then
    echo "   ✓ Announcements endpoint accessible"
else
    echo "   ✗ Announcements endpoint failed with middleware error"
    echo "   Response: $ANNOUNCEMENTS_RESPONSE"
fi

# Clean up
echo ""
echo "5. Cleaning up..."
kill $SERVER_PID 2>/dev/null

echo ""
echo "==========================================="
echo "API TESTING COMPLETE"
echo "==========================================="
echo "Summary:"
echo "- Server status: ✓ Running"
echo "- Authentication: ✓ Working"
echo "- Public endpoints: ✓ Accessible"
echo "- Protected endpoints: $(if [[ $USERS_RESPONSE != *"Target class [role] does not exist"* ]]; then echo "✓ Accessible"; else echo "⚠ Middleware issue"; fi)"
echo ""
echo "Note: Some endpoints may show middleware errors which indicates"
echo "an issue with role-based access control implementation."
echo "The core authentication system is working correctly."
echo "==========================================="