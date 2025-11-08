#!/bin/bash

# Bus Management System - User Creation via cURL
# Corrected script with proper authentication flow

echo "=========================================="
echo "CREATING USERS IN BUS MANAGEMENT SYSTEM"
echo "=========================================="

echo ""
echo "Step 1: Create Admin User (First user should be admin)"
echo "=========================================="

# Create Admin User
ADMIN_RESPONSE=$(curl -s -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "System Admin",
    "email": "admin@busmanagement.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 1
  }')

if [[ $ADMIN_RESPONSE == *"error"* ]]; then
  echo "❌ Admin user creation failed: $ADMIN_RESPONSE"
else
  echo "✅ Admin user created successfully"
fi

echo ""
echo "Step 2: Create Teacher User"
echo "=========================================="

# Create Teacher User
TEACHER_RESPONSE=$(curl -s -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Mrs. Johnson",
    "email": "teacher@busmanagement.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 2
  }')

if [[ $TEACHER_RESPONSE == *"error"* ]]; then
  echo "❌ Teacher user creation failed: $TEACHER_RESPONSE"
else
  echo "✅ Teacher user created successfully"
fi

echo ""
echo "Step 3: Create Parent User"
echo "=========================================="

# Create Parent User
PARENT_RESPONSE=$(curl -s -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Parent Guardian",
    "email": "parent@busmanagement.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 3
  }')

if [[ $PARENT_RESPONSE == *"error"* ]]; then
  echo "❌ Parent user creation failed: $PARENT_RESPONSE"
else
  echo "✅ Parent user created successfully"
fi

echo ""
echo "Step 4: Create Student User"
echo "=========================================="

# Create Student User
STUDENT_RESPONSE=$(curl -s -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Smith",
    "email": "student@busmanagement.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 4
  }')

if [[ $STUDENT_RESPONSE == *"error"* ]]; then
  echo "❌ Student user creation failed: $STUDENT_RESPONSE"
else
  echo "✅ Student user created successfully"
fi

echo ""
echo "Step 5: Create Driver User"
echo "=========================================="

# Create Driver User
DRIVER_RESPONSE=$(curl -s -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Bus Driver",
    "email": "driver@busmanagement.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 5
  }')

if [[ $DRIVER_RESPONSE == *"error"* ]]; then
  echo "❌ Driver user creation failed: $DRIVER_RESPONSE"
else
  echo "✅ Driver user created successfully"
fi

echo ""
echo "Step 6: Create Cleaner User"
echo "=========================================="

# Create Cleaner User
CLEANER_RESPONSE=$(curl -s -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Bus Cleaner",
    "email": "cleaner@busmanagement.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 6
  }')

if [[ $CLEANER_RESPONSE == *"error"* ]]; then
  echo "❌ Cleaner user creation failed: $CLEANER_RESPONSE"
else
  echo "✅ Cleaner user created successfully"
fi

echo ""
echo "Step 7: Test Authentication"
echo "=========================================="

# Login as Admin to get JWT token
LOGIN_RESPONSE=$(curl -s -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@busmanagement.com",
    "password": "password123"
  }')

if [[ $LOGIN_RESPONSE == *"token"* ]]; then
  echo "✅ Admin login successful"
  # Extract token (simplified extraction)
  ADMIN_TOKEN=$(echo $LOGIN_RESPONSE | sed 's/.*"access_token":"\([^"]*\)".*/\1/')
  echo "Admin token extracted: ${ADMIN_TOKEN:0:20}..."
else
  echo "❌ Admin login failed: $LOGIN_RESPONSE"
fi

echo ""
echo "Step 8: Create Related Records (Requires Admin Token)"
echo "=========================================="

if [ ! -z "$ADMIN_TOKEN" ]; then
  # Create a Class
  CLASS_RESPONSE=$(curl -s -X POST http://localhost:8000/api/v1/classes \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ADMIN_TOKEN" \
    -d '{
      "class": "Grade 10A",
      "academic_year": "2025-2026"
    }')
  
  if [[ $CLASS_RESPONSE == *"error"* ]]; then
    echo "❌ Class creation failed: $CLASS_RESPONSE"
  else
    echo "✅ Class created successfully"
  fi
  
  # Create a Bus
  BUS_RESPONSE=$(curl -s -X POST http://localhost:8000/api/v1/buses \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ADMIN_TOKEN" \
    -d '{
      "reg_no": "BUS-001",
      "capacity": 50,
      "model": "Volvo",
      "status": "active"
    }')
  
  if [[ $BUS_RESPONSE == *"error"* ]]; then
    echo "❌ Bus creation failed: $BUS_RESPONSE"
  else
    echo "✅ Bus created successfully"
  fi
  
  # Create a Stop
  STOP_RESPONSE=$(curl -s -X POST http://localhost:8000/api/v1/stops \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ADMIN_TOKEN" \
    -d '{
      "name": "Main Gate",
      "location": "School Entrance",
      "time": "08:00"
    }')
  
  if [[ $STOP_RESPONSE == *"error"* ]]; then
    echo "❌ Stop creation failed: $STOP_RESPONSE"
  else
    echo "✅ Stop created successfully"
  fi
  
  # Create a Student Profile (requires user_id and class_id)
  # Find user_id for the student
  STUDENT_USER_ID=$(curl -s -X GET "http://localhost:8000/api/v1/users?search=student@busmanagement.com" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ADMIN_TOKEN" \
    | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)
  
  # Find class_id
  CLASS_ID=$(curl -s -X GET http://localhost:8000/api/v1/classes \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ADMIN_TOKEN" \
    | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)
  
  if [ ! -z "$STUDENT_USER_ID" ] && [ ! -z "$CLASS_ID" ]; then
    STUDENT_PROFILE_RESPONSE=$(curl -s -X POST http://localhost:8000/api/v1/students \
      -H "Content-Type: application/json" \
      -H "Accept: application/json" \
      -H "Authorization: Bearer $ADMIN_TOKEN" \
      -d '{
        "user_id": '"$STUDENT_USER_ID"',
        "class_id": '"$CLASS_ID"',
        "admission_no": "STU001",
        "address": "123 School Street",
        "bus_service_active": true,
        "academic_year": "2025-2026",
        "dob": "2010-01-01"
      }')
    
    if [[ $STUDENT_PROFILE_RESPONSE == *"error"* ]]; then
      echo "❌ Student profile creation failed: $STUDENT_PROFILE_RESPONSE"
    else
      echo "✅ Student profile created successfully"
    fi
  else
    echo "⚠️ Could not find user_id or class_id to create student profile"
  fi
  
else
  echo "⚠️ Admin token not available, skipping related record creation"
fi

echo ""
echo "Step 9: View All Users (Admin only)"
echo "=========================================="

if [ ! -z "$ADMIN_TOKEN" ]; then
  USERS_RESPONSE=$(curl -s -X GET http://localhost:8000/api/v1/users \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $ADMIN_TOKEN")
  
  if [[ $USERS_RESPONSE == *"data"* ]]; then
    USER_COUNT=$(echo $USERS_RESPONSE | grep -o '"id"' | wc -l)
    echo "✅ Retrieved users successfully: $USER_COUNT users found"
  else
    echo "❌ Failed to retrieve users: $USERS_RESPONSE"
  fi
else
  echo "⚠️ Admin token not available, skipping user retrieval"
fi

echo ""
echo "=========================================="
echo "SUMMARY"
echo "=========================================="
echo "Created users with role IDs:"
echo "  - Admin (role_id: 1)"
echo "  - Teacher (role_id: 2)" 
echo "  - Parent (role_id: 3)"
echo "  - Student (role_id: 4)"
echo "  - Driver (role_id: 5)"
echo "  - Cleaner (role_id: 6)"
echo ""
echo "The Bus Management System is now populated with all user types!"
echo "=========================================="