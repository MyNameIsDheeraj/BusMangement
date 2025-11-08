# Bus Management System - User Creation via cURL
echo "Admin"
## 1. Create Admin User
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
echo
echo
echo "Teacher"

## 2. Create Teacher User
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Teacher User",
    "email": "teacher@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 2
  }'
echo
echo
echo "Parent"

## 3. Create Parent User
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Parent User",
    "email": "parent@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 3
  }'
echo
echo
echo "Student"
echo

## 4. Create Student User
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Dheeraj S",
    "email": "student@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 4
  }'
echo
echo
echo "Driver"
echo

## 5. Create Driver User
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Driver User",
    "email": "driver@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 5
  }'
echo
echo
echo "Cleaner"
echo

## 6. Create Cleaner User
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Cleaner User",
    "email": "cleaner@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 6
  }'
echo
