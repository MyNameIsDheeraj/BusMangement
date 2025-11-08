# Bus Management System - Complete cURL API Examples

## Authentication and User Management Examples

# 1. CREATE USERS OF ALL TYPES

## Create Admin User
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Admin User",
    "email": "admin@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 1
  }'

## Create Teacher User
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Mrs. Johnson",
    "email": "teacher@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 2
  }'

## Create Parent User
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Parent Guardian",
    "email": "parent@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 3
  }'

## Create Student User
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Smith",
    "email": "student@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 4
  }'

## Create Driver User
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Bus Driver",
    "email": "driver@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 5
  }'

## Create Cleaner User
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Cleaner Staff",
    "email": "cleaner@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 6
  }'

# 2. AUTHENTICATION EXAMPLES

## Login as Admin (Get JWT Token)
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123"
  }'

# Example response: {"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...", "token_type": "bearer", "expires_in": 3600, "user": {...}}

## Login as other users
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "teacher@example.com",
    "password": "password123"
  }'

# 3. CREATE ADDITIONAL RECORDS (REQUIRES ADMIN TOKEN)

## Example: Get admin token first (replace YOUR_JWT_TOKEN with actual token from login response)
ADMIN_TOKEN="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..." # Replace with actual token

## Create a Class
curl -X POST http://localhost:8000/api/v1/classes \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "class": "Grade 10A",
    "academic_year": "2025-2026"
  }'

## Create a Bus
curl -X POST http://localhost:8000/api/v1/buses \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "reg_no": "BUS-001",
    "capacity": 50,
    "model": "Volvo",
    "status": "active",
    "driver_id": 5,
    "cleaner_id": 6
  }'

## Create a Route
curl -X POST http://localhost:8000/api/v1/routes \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "name": "Route 1",
    "description": "Main route from downtown to school"
  }'

## Create a Stop
curl -X POST http://localhost:8000/api/v1/stops \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "name": "Main Gate",
    "location": "School Entrance",
    "time": "08:00",
    "latitude": 40.7128,
    "longitude": -74.0060
  }'

## Create a Student Profile (after creating users and classes)
curl -X POST http://localhost:8000/api/v1/students \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "user_id": 7,
    "class_id": 1,
    "admission_no": "STU001",
    "address": "123 School Street",
    "pickup_stop_id": 1,
    "drop_stop_id": 2,
    "bus_service_active": true,
    "academic_year": "2025-2026",
    "dob": "2010-01-01"
  }'

## Create a Parent Profile (links to user)
curl -X POST http://localhost:8000/api/v1/parents \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "user_id": 3
  }'

## Create a Teacher Profile (links to user)
curl -X POST http://localhost:8000/api/v1/teachers \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "user_id": 2
  }'

## Create a Staff Profile (for driver/cleaner)
curl -X POST http://localhost:8000/api/v1/staff-profiles \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "user_id": 5,
    "salary": 3500.00,
    "license_number": "DL123456",
    "emergency_contact": "Jane Smith - 9876543210",
    "bus_id": 1
  }'

# 4. VIEW RECORDS

## Get all users (admin only)
curl -X GET http://localhost:8000/api/v1/users \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

## Get all students
curl -X GET http://localhost:8000/api/v1/students \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

## Get all buses
curl -X GET http://localhost:8000/api/v1/buses \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

## Get current user info
curl -X GET http://localhost:8000/api/v1/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

# 5. UPDATE RECORDS

## Update a user (admin only)
curl -X PUT http://localhost:8000/api/v1/users/7 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "name": "John Smith Updated",
    "email": "john.smith@example.com"
  }'

## Update a student profile
curl -X PUT http://localhost:8000/api/v1/students/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "address": "456 Updated Street",
    "bus_service_active": false
  }'

# 6. DELETE RECORDS (admin only)

## Delete a user
curl -X DELETE http://localhost:8000/api/v1/users/7 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

## Delete a student
curl -X DELETE http://localhost:8000/api/v1/students/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

# 7. SPECIAL OPERATIONS

## Assign parent to student
curl -X POST http://localhost:8000/api/v1/students/1/assign-parent \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "parent_id": 3
  }'

## Bulk delete students
curl -X POST http://localhost:8000/api/v1/students/bulk-delete \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "ids": [1, 2, 3]
  }'

# 8. PAYMENTS, ATTENDANCE, ALERTS, ANNOUNCEMENTS

## Create a payment
curl -X POST http://localhost:8000/api/v1/payments \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "student_id": 1,
    "amount": 100.00,
    "payment_method": "card",
    "transaction_id": "txn_123456789",
    "payment_date": "2025-01-15",
    "status": "completed",
    "description": "Bus fee payment"
  }'

## Create an attendance record
curl -X POST http://localhost:8000/api/v1/attendances \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "student_id": 1,
    "bus_id": 1,
    "date": "2025-01-15",
    "status": "present",
    "marked_by": 1
  }'

## Create an alert
curl -X POST http://localhost:8000/api/v1/alerts \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "title": "Bus Delay",
    "description": "The bus is delayed by 15 minutes",
    "type": "warning",
    "priority": "high",
    "student_id": 1,
    "submitted_by": 1
  }'

## Create an announcement
curl -X POST http://localhost:8000/api/v1/announcements \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "title": "School Event",
    "content": "School will be closed on Monday for maintenance",
    "priority": "high",
    "start_date": "2025-01-20",
    "end_date": "2025-01-21",
    "target_roles": ["student", "parent"]
  }'

# NOTES:
# - Replace http://localhost:8000 with your actual server URL
# - Replace YOUR_JWT_TOKEN with the actual JWT token obtained from login
# - Role IDs: 1=admin, 2=teacher, 3=parent, 4=student, 5=driver, 6=cleaner
# - Ensure dependencies exist before creating related records (e.g., create user before student profile)
# - All non-public endpoints require JWT authentication in Authorization header
# - Admin tokens are required for most operations except user-specific endpoints