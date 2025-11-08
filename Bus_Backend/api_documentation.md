# Student Management API Documentation

This document provides comprehensive documentation for the student management API with role-based permissions and JWT authentication.

## Authentication

All protected endpoints require a valid JWT token in the Authorization header:

```
Authorization: Bearer {jwt_token}
```

### Login
```
POST /api/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

Response:
```json
{
    "access_token": "jwt_token_here",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "role": {
            "id": 1,
            "name": "admin"
        }
    },
    "role": "admin"
}
```

### Register
```
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password",
    "password_confirmation": "password",
    "role_id": 1
}
```

## Student Management Endpoints

### Get All Students
```
GET /api/students
Authorization: Bearer {jwt_token}
```

Query Parameters:
- `search` - Search students by name, admission_no, or address
- `class_id` - Filter by class ID
- `academic_year` - Filter by academic year
- `bus_service_active` - Filter by bus service status (1/0)

Required Permission: `view_student`

### Create Student
```
POST /api/students
Authorization: Bearer {jwt_token}
Content-Type: application/json

{
    "user_id": 1,
    "class_id": 1,
    "admission_no": "STU001",
    "dob": "2010-05-15",
    "address": "123 Main St, City",
    "pickup_stop_id": 1,
    "drop_stop_id": 1,
    "academic_year": "2024-2025",
    "bus_service_active": true
}
```

Required Permission: `create_student`
Required Role: `admin`

### Get Single Student
```
GET /api/students/{id}
Authorization: Bearer {jwt_token}
```

Required Permission: `view_student`

### Update Student
```
PUT /api/students/{id}
Authorization: Bearer {jwt_token}
Content-Type: application/json

{
    "class_id": 2,
    "address": "456 New Address St, City",
    "bus_service_active": false
}
```

Required Permission: `edit_student`
Allowed Roles: `admin`, `teacher`, `parent` (with restrictions)

### Delete Student
```
DELETE /api/students/{id}
Authorization: Bearer {jwt_token}
```

Required Permission: `delete_student`
Required Role: `admin`

### Bulk Delete Students
```
POST /api/students/bulk-delete
Authorization: Bearer {jwt_token}
Content-Type: application/json

{
    "ids": [1, 2, 3]
}
```

Required Permission: `delete_student`
Required Role: `admin`

### Get Students by Class
```
GET /api/students/class/{classId}
Authorization: Bearer {jwt_token}
```

Required Permission: `view_student`
Allowed Roles: `admin`, `teacher` (for their classes)

### Assign Parent to Student
```
POST /api/students/{studentId}/assign-parent
Authorization: Bearer {jwt_token}
Content-Type: application/json

{
    "parent_id": 1
}
```

Required Permission: `edit_student`
Required Role: `admin`

### Remove Parent from Student
```
DELETE /api/students/{studentId}/remove-parent/{parentId}
Authorization: Bearer {jwt_token}
```

Required Permission: `edit_student`
Required Role: `admin`

## Student-Parent Relationship Endpoints

### Get All Relationships
```
GET /api/student-parents
Authorization: Bearer {jwt_token}
```

Query Parameters:
- `student_id` - Filter by student ID
- `parent_id` - Filter by parent ID

Required Permission: `view_student`

### Create Relationship
```
POST /api/student-parents
Authorization: Bearer {jwt_token}
Content-Type: application/json

{
    "student_id": 1,
    "parent_id": 1
}
```

Required Permission: `edit_student`
Required Role: `admin`

### Get Specific Relationship
```
GET /api/student-parents/{id}
Authorization: Bearer {jwt_token}
```

Required Permission: `view_student`

### Delete Relationship
```
DELETE /api/student-parents/{id}
Authorization: Bearer {jwt_token}
```

Required Permission: `delete_student`
Required Role: `admin`

### Get Parents for a Student
```
GET /api/student-parents/student/{studentId}
Authorization: Bearer {jwt_token}
```

Required Permission: `view_student`

### Get Students for a Parent
```
GET /api/student-parents/parent/{parentId}
Authorization: Bearer {jwt_token}
```

Required Permission: `view_student`

## Role-Based Access Control

The system implements role-based permissions with the following roles:

### Admin
- Full access to all student management features
- Permissions: `view_student`, `create_student`, `edit_student`, `delete_student`

### Teacher
- Can view students in their assigned classes
- Can edit student information for students in their classes
- Can view attendance for students in their classes
- Permissions: `view_student`, `edit_student`

### Parent
- Can view their children's information
- Can create payments for their children
- Permissions: `view_student`

### Student
- Can view their own information
- Permissions: `view_student`

### Driver/Cleaner
- Can view students on their assigned bus route
- Can mark attendance for students on their route
- Permissions: `view_student`, `mark_attendance`, `create_alert`

## Validation Rules

### Student Creation/Update
- `user_id`: required, must exist in users table
- `class_id`: required, must exist in classes table
- `admission_no`: required, unique
- `dob`: optional, must be valid date format
- `address`: optional, max 500 characters
- `pickup_stop_id`: optional, must exist in stops table
- `drop_stop_id`: optional, must exist in stops table
- `academic_year`: required, format: YYYY-YYYY (e.g., 2024-2025)
- `bus_service_active`: optional, boolean

### Student-Parent Relationship
- `student_id`: required, must exist in students table
- `parent_id`: required, must exist in parents table

## Error Responses

Standard error response format:
```json
{
    "error": "Error message here"
}
```

Common HTTP status codes:
- 200: Success
- 201: Created
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 500: Server Error