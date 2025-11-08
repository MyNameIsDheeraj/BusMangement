# Updated API Endpoint Documentation - Bus Management System (Role-Based Access)

This document provides comprehensive documentation for all API endpoints in the Bus Management System with proper role-based access controls. All endpoints now follow appropriate role-based access patterns.

## Table of Contents
1. [Authentication Endpoints](#authentication-endpoints)
2. [User Management Endpoints](#user-management-endpoints)
3. [Student Management Endpoints](#student-management-endpoints)
4. [Student-Parent Relationship Endpoints](#student-parent-relationship-endpoints)
5. [Bus Management Endpoints](#bus-management-endpoints)
6. [Route Management Endpoints](#route-management-endpoints)
7. [Stop Management Endpoints](#stop-management-endpoints)
8. [Payment Management Endpoints](#payment-management-endpoints)
9. [Attendance Management Endpoints](#attendance-management-endpoints)
10. [Alert Management Endpoints](#alert-management-endpoints)
11. [Announcement Management Endpoints](#announcement-management-endpoints)
12. [Staff Management Endpoints](#staff-management-endpoints)
13. [Teacher Management Endpoints](#teacher-management-endpoints)
14. [Parent Management Endpoints](#parent-management-endpoints)
15. [Testing Procedures](#testing-procedures)

## Authentication Endpoints

### 1. Test Endpoint
- **Method:** `GET`
- **URL:** `/api/v1/test`
- **Authentication:** Public
- **Description:** Simple test endpoint to verify API is working

### 2. Login
- **Method:** `POST`
- **URL:** `/api/v1/login`
- **Authentication:** Public
- **Description:** Authenticate user and return JWT token
- **Request Body:**
```json
{
    "email": "admin@example.com",
    "password": "password"
}
```
- **Response:**
```json
{
    "access_token": "jwt_token_here",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com",
        "role": {
            "id": 1,
            "name": "admin"
        }
    },
    "role": "admin"
}
```

### 3. Admin-Only User Registration
- **Method:** `POST`
- **URL:** `/api/v1/register`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Register a new user (only accessible by admin users)
- **Headers:** `Authorization: Bearer <admin_token>`
- **Request Body:**
```json
{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 2
}
```
- **Response:**
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 7,
        "name": "New User",
        "email": "newuser@example.com",
        "role": "teacher"
    }
}
```

### 4. Get User Info
- **Method:** `GET`
- **URL:** `/api/v1/me`
- **Authentication:** JWT Bearer token required
- **Description:** Get authenticated user details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** User object with role information

### 5. Logout
- **Method:** `POST`
- **URL:** `/api/v1/logout`
- **Authentication:** JWT Bearer token required
- **Description:** Invalidate JWT token
- **Headers:** `Authorization: Bearer <token>`
- **Response:** `{"message": "Successfully logged out"}`

### 6. Refresh Token
- **Method:** `POST`
- **URL:** `/api/v1/refresh`
- **Authentication:** JWT Bearer token required
- **Description:** Refresh expired JWT token
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Updated token and user information

## User Management Endpoints (Admin Only)

### 7. Get All Users (Admin Only)
- **Method:** `GET`
- **URL:** `/api/v1/users`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Get paginated list of all users
- **Headers:** `Authorization: Bearer <token>`
- **Query Parameters:** `page`, `per_page`
- **Response:** Paginated list of users with roles

### 8. Get Single User (Admin Only)
- **Method:** `GET`
- **URL:** `/api/v1/users/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Get specific user details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** User object with role information

### 9. Create User (Admin Only)
- **Method:** `POST`
- **URL:** `/api/v1/users`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Create a new user
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password123",
    "mobile": "1234567890",
    "role_id": 2
}
```

### 10. Update User (Admin Only)
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/users/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Update user details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full user data

### 11. Delete User (Admin Only)
- **Method:** `DELETE`
- **URL:** `/api/v1/users/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Delete a user account
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Student Management Endpoints

### 12. Get All Students
- **Method:** `GET`
- **URL:** `/api/v1/students`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Permissions:** `view_student`
- **Description:** Get paginated list of students
- **Headers:** `Authorization: Bearer <token>`
- **Query Parameters:**
  - `search`: Search by name, admission_no, or address
  - `class_id`: Filter by class ID
  - `academic_year`: Filter by academic year
  - `bus_service_active`: Filter by bus service status (1/0)
- **Response:** Paginated list of students with user details

### 13. Get Single Student
- **Method:** `GET`
- **URL:** `/api/v1/students/{id}`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Permissions:** `view_student`
- **Description:** Get specific student details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Student object with user, class, stops, and parent information

### 14. Create Student (Admin Only)
- **Method:** `POST`
- **URL:** `/api/v1/students`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `create_student`
- **Description:** Create a new student
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "user_id": 1,
    "class_id": 1,
    "admission_no": "STU001",
    "dob": "2010-05-15",
    "address": "123 Main St",
    "pickup_stop_id": 1,
    "drop_stop_id": 1,
    "academic_year": "2024-2025",
    "bus_service_active": true
}
```
- **Response:** Created student object

### 15. Update Student
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/students/{id}`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent roles)
- **Permissions:** `edit_student`
- **Description:** Update student details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full student data

### 16. Delete Student (Admin Only)
- **Method:** `DELETE`
- **URL:** `/api/v1/students/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `delete_student`
- **Description:** Delete a student record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

### 17. Bulk Delete Students (Admin Only)
- **Method:** `POST`
- **URL:** `/api/v1/students/bulk-delete`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `delete_student`
- **Description:** Delete multiple students at once
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "ids": [1, 2, 3]
}
```

### 18. Assign Parent to Student (Admin Only)
- **Method:** `POST`
- **URL:** `/api/v1/students/{studentId}/assign-parent`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `edit_student`
- **Description:** Assign a parent to a student
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "parent_id": 1
}
```

### 19. Remove Parent from Student (Admin Only)
- **Method:** `DELETE`
- **URL:** `/api/v1/students/{studentId}/remove-parent/{parentId}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `edit_student`
- **Description:** Remove parent assignment from student
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

### 20. Get Students by Class
- **Method:** `GET`
- **URL:** `/api/v1/students/class/{classId}`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Permissions:** `view_student`
- **Description:** Get all students in a specific class
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Array of students in the class

## Student-Parent Relationship Endpoints

### 21. Get All Relationships
- **Method:** `GET`
- **URL:** `/api/v1/student-parents`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Permissions:** `view_student`
- **Description:** Get all student-parent relationships
- **Headers:** `Authorization: Bearer <token>`
- **Query Parameters:**
  - `student_id`: Filter by student ID
  - `parent_id`: Filter by parent ID
- **Response:** Paginated list of relationships

### 22. Create Relationship (Admin Only)
- **Method:** `POST`
- **URL:** `/api/v1/student-parents`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `edit_student`
- **Description:** Create a new student-parent relationship
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "student_id": 1,
    "parent_id": 1
}
```

### 23. Get Specific Relationship
- **Method:** `GET`
- **URL:** `/api/v1/student-parents/{id}`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Permissions:** `view_student`
- **Description:** Get specific student-parent relationship
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Relationship object with student and parent details

### 24. Delete Relationship (Admin Only)
- **Method:** `DELETE`
- **URL:** `/api/v1/student-parents/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `delete_student`
- **Description:** Remove student-parent relationship
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

### 25. Get Parents for Student
- **Method:** `GET`
- **URL:** `/api/v1/student-parents/student/{studentId}`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Permissions:** `view_student`
- **Description:** Get all parents for a specific student
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Array of parent objects

### 26. Get Students for Parent
- **Method:** `GET`
- **URL:** `/api/v1/student-parents/parent/{parentId}`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Permissions:** `view_student`
- **Description:** Get all students for a specific parent
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Array of student objects

## Bus Management Endpoints

### 27. Get All Buses
- **Method:** `GET`
- **URL:** `/api/v1/buses`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student, Driver, Cleaner roles)
- **Description:** Get paginated list of buses based on user role
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of buses with driver and cleaner info
- **Access Levels:**
  - Admin: All buses
  - Driver: Only assigned bus
  - Cleaner: Only assigned bus
  - Teacher: Buses related to their classes
  - Parent: Buses for their children
  - Student: Their assigned bus

### 28. Get Single Bus
- **Method:** `GET`
- **URL:** `/api/v1/buses/{id}`
- **Authentication:** JWT Bearer token required
- **Description:** Get specific bus details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Bus object with driver, cleaner, and route information

### 29. Create Bus (Admin Only)
- **Method:** `POST`
- **URL:** `/api/v1/buses`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Create a new bus
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "bus_number": "BUS001",
    "registration_no": "REG001",
    "model": "Toyota Coaster",
    "seating_capacity": 25,
    "status": true,
    "driver_id": 1,
    "cleaner_id": 2
}
```

### 30. Update Bus (Admin Only)
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/buses/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Update bus details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full bus data

### 31. Delete Bus (Admin Only)
- **Method:** `DELETE`
- **URL:** `/api/v1/buses/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Delete a bus record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Route Management Endpoints

### 32. Get All Routes
- **Method:** `GET`
- **URL:** `/api/v1/routes`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student, Driver, Cleaner roles)
- **Description:** Get paginated list of routes based on user role
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of routes with bus information

### 33. Get Single Route
- **Method:** `GET`
- **URL:** `/api/v1/routes/{id}`
- **Authentication:** JWT Bearer token required
- **Description:** Get specific route details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Route object with bus and stops information

### 34. Create Route (Admin Only)
- **Method:** `POST`
- **URL:** `/api/v1/routes`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Create a new route
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "bus_id": 1,
    "name": "Route A",
    "total_kilometer": 15.5,
    "start_time": "07:30:00",
    "end_time": "08:30:00",
    "academic_year": "2024-2025"
}
```

### 35. Update Route (Admin Only)
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/routes/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Update route details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full route data

### 36. Delete Route (Admin Only)
- **Method:** `DELETE`
- **URL:** `/api/v1/routes/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Delete a route record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Stop Management Endpoints

### 37. Get All Stops
- **Method:** `GET`
- **URL:** `/api/v1/stops`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student, Driver, Cleaner roles)
- **Description:** Get paginated list of stops
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of stops

### 38. Get Single Stop
- **Method:** `GET`
- **URL:** `/api/v1/stops/{id}`
- **Authentication:** JWT Bearer token required
- **Description:** Get specific stop details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Stop object

### 39. Create Stop (Admin Only)
- **Method:** `POST`
- **URL:** `/api/v1/stops`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Create a new stop
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "name": "Main Gate",
    "location": "School Main Gate",
    "route_id": 1,
    "order": 1,
    "time": "07:30:00"
}
```

### 40. Update Stop (Admin Only)
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/stops/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Update stop details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full stop data

### 41. Delete Stop (Admin Only)
- **Method:** `DELETE`
- **URL:** `/api/v1/stops/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Delete a stop record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Payment Management Endpoints

### 42. Get All Payments
- **Method:** `GET`
- **URL:** `/api/v1/payments`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Description:** Get paginated list of payments
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of payments

### 43. Get Single Payment
- **Method:** `GET`
- **URL:** `/api/v1/payments/{id}`
- **Authentication:** JWT Bearer token required
- **Description:** Get specific payment details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Payment object

### 44. Create Payment
- **Method:** `POST`
- **URL:** `/api/v1/payments`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent roles)
- **Description:** Create a new payment
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "student_id": 1,
    "amount_paid": 500.00,
    "total_amount_due": 500.00,
    "payment_type": "bus_fee",
    "payment_date": "2024-01-15",
    "status": "paid"
}
```

### 45. Update Payment (Admin Only)
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/payments/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Update payment details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full payment data

### 46. Delete Payment (Admin Only)
- **Method:** `DELETE`
- **URL:** `/api/v1/payments/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Delete a payment record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Attendance Management Endpoints

### 47. Get All Attendances
- **Method:** `GET`
- **URL:** `/api/v1/attendances`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student, Driver, Cleaner roles)
- **Description:** Get paginated list of attendances
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of attendances

### 48. Get Single Attendance
- **Method:** `GET`
- **URL:** `/api/v1/attendances/{id}`
- **Authentication:** JWT Bearer token required
- **Description:** Get specific attendance details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Attendance object

### 49. Create Attendance
- **Method:** `POST`
- **URL:** `/api/v1/attendances`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Driver, Cleaner roles)
- **Description:** Create a new attendance record
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "student_id": 1,
    "bus_id": 1,
    "date": "2024-01-15",
    "status": "present",
    "academic_year": "2024-2025"
}
```

### 50. Update Attendance
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/attendances/{id}`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Driver, Cleaner roles)
- **Description:** Update attendance details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full attendance data

### 51. Delete Attendance (Admin Only)
- **Method:** `DELETE`
- **URL:** `/api/v1/attendances/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Delete an attendance record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Alert Management Endpoints

### 52. Get All Alerts
- **Method:** `GET`
- **URL:** `/api/v1/alerts`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student, Driver, Cleaner roles)
- **Description:** Get paginated list of alerts
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of alerts

### 53. Get Single Alert
- **Method:** `GET`
- **URL:** `/api/v1/alerts/{id}`
- **Authentication:** JWT Bearer token required
- **Description:** Get specific alert details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Alert object

### 54. Create Alert
- **Method:** `POST`
- **URL:** `/api/v1/alerts`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Driver, Cleaner roles)
- **Description:** Create a new alert
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "description": "Bus Delay",
    "submitted_by": 1,
    "student_id": 1,
    "bus_id": 1,
    "route_id": 1,
    "severity": "high",
    "status": "new"
}
```

### 55. Update Alert
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/alerts/{id}`
- **Authentication:** JWT Bearer token required (Admin, Teacher roles)
- **Description:** Update alert details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full alert data

### 56. Delete Alert (Admin Only)
- **Method:** `DELETE`
- **URL:** `/api/v1/alerts/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Delete an alert record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Announcement Management Endpoints

### 57. Get All Announcements
- **Method:** `GET`
- **URL:** `/api/v1/announcements`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Description:** Get paginated list of announcements
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of announcements

### 58. Get Single Announcement
- **Method:** `GET`
- **URL:** `/api/v1/announcements/{id}`
- **Authentication:** JWT Bearer token required
- **Description:** Get specific announcement details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Announcement object

### 59. Create Announcement
- **Method:** `POST`
- **URL:** `/api/v1/announcements`
- **Authentication:** JWT Bearer token required (Admin, Teacher roles)
- **Description:** Create a new announcement
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "title": "School Holiday",
    "description": "School will be closed on Monday for holiday",
    "created_by": 1,
    "audience": "all",
    "is_active": true
}
```

### 60. Update Announcement
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/announcements/{id}`
- **Authentication:** JWT Bearer token required (Admin, Teacher roles)
- **Description:** Update announcement details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full announcement data

### 61. Delete Announcement (Admin Only)
- **Method:** `DELETE`
- **URL:** `/api/v1/announcements/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Delete an announcement record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Staff Management Endpoints

### 62. Get All Staff Profiles
- **Method:** `GET`
- **URL:** `/api/v1/staff-profiles`
- **Authentication:** JWT Bearer token required (Admin, Driver, Cleaner roles)
- **Permissions:** `view_staff`
- **Description:** Get paginated list of staff profiles
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of staff profiles

### 63. Get Single Staff Profile
- **Method:** `GET`
- **URL:** `/api/v1/staff-profiles/{id}`
- **Authentication:** JWT Bearer token required (Admin, Driver, Cleaner roles)
- **Permissions:** `view_staff`
- **Description:** Get specific staff profile details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Staff profile object

### 64. Create Staff Profile (Admin Only)
- **Method:** `POST`
- **URL:** `/api/v1/staff-profiles`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `create_staff`
- **Description:** Create a new staff profile
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "user_id": 1,
    "position": "Driver",
    "hire_date": "2024-01-01",
    "salary": 25000.00,
    "contact_number": "1234567890"
}
```

### 65. Update Staff Profile (Admin Only)
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/staff-profiles/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `edit_staff`
- **Description:** Update staff profile details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full staff profile data

### 66. Delete Staff Profile (Admin Only)
- **Method:** `DELETE`
- **URL:** `/api/v1/staff-profiles/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `delete_staff`
- **Description:** Delete a staff profile record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Teacher Management Endpoints

### 67. Get All Teachers
- **Method:** `GET`
- **URL:** `/api/v1/teachers`
- **Authentication:** JWT Bearer token required (Admin, Teacher roles)
- **Permissions:** `view_users`
- **Description:** Get paginated list of teachers
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of teachers

### 68. Get Single Teacher
- **Method:** `GET`
- **URL:** `/api/v1/teachers/{id}`
- **Authentication:** JWT Bearer token required (Admin, Teacher roles)
- **Permissions:** `view_users`
- **Description:** Get specific teacher details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Teacher object

### 69. Create Teacher (Admin Only)
- **Method:** `POST`
- **URL:** `/api/v1/teachers`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `create_users`
- **Description:** Create a new teacher account
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "name": "Teacher Name",
    "email": "teacher@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 2
}
```
- **Response:** Success message with teacher details

### 70. Update Teacher (Admin Only)
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/teachers/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `edit_users`
- **Description:** Update teacher details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full teacher data

### 71. Delete Teacher (Admin Only)
- **Method:** `DELETE`
- **URL:** `/api/v1/teachers/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `delete_users`
- **Description:** Delete a teacher record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

### 72. Get My Classes (Teacher Only)
- **Method:** `GET`
- **URL:** `/api/v1/teachers/me/classes`
- **Authentication:** JWT Bearer token required (Teacher role)
- **Description:** Get classes assigned to the current teacher
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Array of classes

### 73. Get My Students (Teacher Only)
- **Method:** `GET`
- **URL:** `/api/v1/teachers/me/students`
- **Authentication:** JWT Bearer token required (Teacher role)
- **Description:** Get students in classes assigned to the current teacher
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Array of students

## Parent Management Endpoints

### 74. Get All Parents
- **Method:** `GET`
- **URL:** `/api/v1/parents`
- **Authentication:** JWT Bearer token required (Admin, Parent roles)
- **Permissions:** `view_users`
- **Description:** Get paginated list of parents
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of parents

### 75. Get Single Parent
- **Method:** `GET`
- **URL:** `/api/v1/parents/{id}`
- **Authentication:** JWT Bearer token required (Admin, Parent roles)
- **Permissions:** `view_users`
- **Description:** Get specific parent details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Parent object

### 76. Create Parent (Admin Only)
- **Method:** `POST`
- **URL:** `/api/v1/parents`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `create_users`
- **Description:** Create a new parent account
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "name": "Parent Name",
    "email": "parent@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 3
}
```
- **Response:** Success message with parent details

### 77. Update Parent (Admin Only)
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/parents/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `edit_users`
- **Description:** Update parent details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full parent data

### 78. Delete Parent (Admin Only)
- **Method:** `DELETE`
- **URL:** `/api/v1/parents/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Permissions:** `delete_users`
- **Description:** Delete a parent record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

### 79. Get My Students (Parent Only)
- **Method:** `GET`
- **URL:** `/api/v1/parents/me/students`
- **Authentication:** JWT Bearer token required (Parent role)
- **Description:** Get students assigned to the current parent
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Array of students

## Testing Procedures

### Pre-requisites
1. Start the Laravel development server:
   ```bash
   cd /home/hellbat/Documents/project/Laravel/Bus_management/BusMangement
   php artisan serve
   ```

2. Use the provided JWT tokens for each role (found in Jwt_users.txt):
   - Admin: `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...`
   - Teacher: `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...`
   - Parent: `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...`
   - Student: `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...`
   - Driver: `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...`
   - Cleaner: `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...`

### Testing Steps for Each Endpoint

#### 1. Authentication Endpoints
- **Test Login**: 
  ```bash
  curl -X POST http://127.0.0.1:8000/api/v1/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
  ```

- **Test Admin Registration (Protected)**:
  ```bash
  curl -X POST http://127.0.0.1:8000/api/v1/register \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -d '{"name":"New User","email":"newuser@example.com","password":"password","password_confirmation":"password","role_id":2}'
  ```

#### 2. Role-Based Access Tests
- **Test Admin-Only endpoint with non-admin user**:
  ```bash
  curl -X POST http://127.0.0.1:8000/api/v1/students \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TEACHER_TOKEN" \
  -d '{"user_id":2,"class_id":1,"admission_no":"STU999","dob":"2010-05-15","address":"Test Address","academic_year":"2024-2025"}'
  ```

- **Test Admin-Only endpoint with admin user (should succeed)**:
  ```bash
  curl -X POST http://127.0.0.1:8000/api/v1/students \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -d '{"user_id":2,"class_id":1,"admission_no":"STU999","dob":"2010-05-15","address":"Test Address","academic_year":"2024-2025"}'
  ```

- **Test endpoint accessible by multiple roles**:
  ```bash
  curl -X GET http://127.0.0.1:8000/api/v1/students \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TEACHER_TOKEN"
  ```

### Key Changes Summary
1. **Admin-Only Operations**: Creation, updates, and deletion of core entities (students, buses, routes, stops, etc.) are restricted to admin users only
2. **Role-Based Access**: Appropriate endpoints remain accessible by specific user roles (e.g., teachers can manage attendance, parents can view their children's information)
3. **Enhanced Security**: Critical operations like creating users and managing system entities are now restricted to administrators
4. **Maintained Functionality**: Regular operations that users need for their roles are still available to them

This implementation ensures proper role-based access control throughout the application while maintaining the necessary functionality for each user type to perform their required tasks.