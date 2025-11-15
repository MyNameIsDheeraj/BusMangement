# Comprehensive API Endpoint Documentation - Bus Management System

This document provides comprehensive documentation for all API endpoints in the Bus Management System, including authentication, authorization, request/response formats, and testing procedures.

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
- **Response:** `{"message": "API is working"}`

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

### 3. Admin-Only User Registration (NEW)
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

### 7. Get All Users
- **Method:** `GET`
- **URL:** `/api/v1/users`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Get paginated list of all users
- **Headers:** `Authorization: Bearer <token>`
- **Query Parameters:** `page`, `per_page`
- **Response:** Paginated list of users with roles

### 8. Get Single User
- **Method:** `GET`
- **URL:** `/api/v1/users/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Get specific user details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** User object with role information

### 9. Update User
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/users/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Update user details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full user data

### 10. Delete User
- **Method:** `DELETE`
- **URL:** `/api/v1/users/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Delete a user account
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Student Management Endpoints

### 11. Get All Students
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

### 12. Get Single Student
- **Method:** `GET`
- **URL:** `/api/v1/students/{id}`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Permissions:** `view_student`
- **Description:** Get specific student details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Student object with user, class, stops, and parent information

### 13. Create Student
- **Method:** `POST`
- **URL:** `/api/v1/students`
- **Authentication:** JWT Bearer token required (Admin role)
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

### 14. Update Student
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/students/{id}`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent roles)
- **Permissions:** `edit_student`
- **Description:** Update student details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full student data

### 15. Delete Student
- **Method:** `DELETE`
- **URL:** `/api/v1/students/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Permissions:** `delete_student`
- **Description:** Delete a student record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

### 16. Bulk Delete Students
- **Method:** `POST`
- **URL:** `/api/v1/students/bulk-delete`
- **Authentication:** JWT Bearer token required (Admin role)
- **Permissions:** `delete_student`
- **Description:** Delete multiple students at once
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "ids": [1, 2, 3]
}
```

### 17. Get Students by Class
- **Method:** `GET`
- **URL:** `/api/v1/students/class/{classId}`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Permissions:** `view_student`
- **Description:** Get all students in a specific class
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Array of students in the class

### 18. Assign Parent to Student
- **Method:** `POST`
- **URL:** `/api/v1/students/{studentId}/assign-parent`
- **Authentication:** JWT Bearer token required (Admin role)
- **Permissions:** `edit_student`
- **Description:** Assign a parent to a student
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "parent_id": 1
}
```

### 19. Remove Parent from Student
- **Method:** `DELETE`
- **URL:** `/api/v1/students/{studentId}/remove-parent/{parentId}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Permissions:** `edit_student`
- **Description:** Remove parent assignment from student
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Student-Parent Relationship Endpoints

### 20. Get All Relationships
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

### 21. Create Relationship
- **Method:** `POST`
- **URL:** `/api/v1/student-parents`
- **Authentication:** JWT Bearer token required (Admin role)
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

### 22. Get Specific Relationship
- **Method:** `GET`
- **URL:** `/api/v1/student-parents/{id}`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Permissions:** `view_student`
- **Description:** Get specific student-parent relationship
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Relationship object with student and parent details

### 23. Delete Relationship
- **Method:** `DELETE`
- **URL:** `/api/v1/student-parents/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Permissions:** `delete_student`
- **Description:** Remove student-parent relationship
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

### 24. Get Parents for Student
- **Method:** `GET`
- **URL:** `/api/v1/student-parents/student/{studentId}`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Permissions:** `view_student`
- **Description:** Get all parents for a specific student
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Array of parent objects

### 25. Get Students for Parent
- **Method:** `GET`
- **URL:** `/api/v1/student-parents/parent/{parentId}`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Permissions:** `view_student`
- **Description:** Get all students for a specific parent
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Array of student objects

## Bus Management Endpoints

### 26. Get All Buses
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

### 27. Get Single Bus
- **Method:** `GET`
- **URL:** `/api/v1/buses/{id}`
- **Authentication:** JWT Bearer token required
- **Description:** Get specific bus details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Bus object with driver, cleaner, and route information

### 28. Create Bus
- **Method:** `POST`
- **URL:** `/api/v1/buses`
- **Authentication:** JWT Bearer token required (Admin role)
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

### 29. Update Bus
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/buses/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Update bus details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full bus data

### 30. Delete Bus
- **Method:** `DELETE`
- **URL:** `/api/v1/buses/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Delete a bus record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Route Management Endpoints

### 31. Get All Routes
- **Method:** `GET`
- **URL:** `/api/v1/routes`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student, Driver, Cleaner roles)
- **Description:** Get paginated list of routes based on user role
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of routes with bus information

### 32. Get Single Route
- **Method:** `GET`
- **URL:** `/api/v1/routes/{id}`
- **Authentication:** JWT Bearer token required
- **Description:** Get specific route details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Route object with bus and stops information

### 33. Create Route
- **Method:** `POST`
- **URL:** `/api/v1/routes`
- **Authentication:** JWT Bearer token required (Admin role)
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

### 34. Update Route
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/routes/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Update route details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full route data

### 35. Delete Route
- **Method:** `DELETE`
- **URL:** `/api/v1/routes/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Delete a route record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Stop Management Endpoints

### 36. Get All Stops
- **Method:** `GET`
- **URL:** `/api/v1/stops`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student, Driver, Cleaner roles)
- **Description:** Get paginated list of stops
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of stops

### 37. Get Single Stop
- **Method:** `GET`
- **URL:** `/api/v1/stops/{id}`
- **Authentication:** JWT Bearer token required
- **Description:** Get specific stop details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Stop object

### 38. Create Stop
- **Method:** `POST`
- **URL:** `/api/v1/stops`
- **Authentication:** JWT Bearer token required (Admin role)
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

### 39. Update Stop
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/stops/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Update stop details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full stop data

### 40. Delete Stop
- **Method:** `DELETE`
- **URL:** `/api/v1/stops/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Delete a stop record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Payment Management Endpoints

### 41. Get All Payments
- **Method:** `GET`
- **URL:** `/api/v1/payments`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Description:** Get paginated list of payments
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of payments

### 42. Get Single Payment
- **Method:** `GET`
- **URL:** `/api/v1/payments/{id}`
- **Authentication:** JWT Bearer token required
- **Description:** Get specific payment details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Payment object

### 43. Create Payment
- **Method:** `POST`
- **URL:** `/api/v1/payments`
- **Authentication:** JWT Bearer token required (Admin, Parent roles)
- **Description:** Create a new payment
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "student_id": 1,
    "amount_paid": 500.00,
    "total_amount_due": 500.00,
    "payment_type": "monthly",
    "payment_date": "2024-01-15",
    "status": "paid",
    "academic_year": "2024-2025",
    "transaction_id": "TXN123456789"
}
```

### 44. Update Payment
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/payments/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Update payment details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full payment data

### 45. Delete Payment
- **Method:** `DELETE`
- **URL:** `/api/v1/payments/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Delete a payment record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Attendance Management Endpoints

### 46. Get All Attendances
- **Method:** `GET`
- **URL:** `/api/v1/attendances`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student, Driver, Cleaner roles)
- **Description:** Get paginated list of attendances
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of attendances

### 47. Get Single Attendance
- **Method:** `GET`
- **URL:** `/api/v1/attendances/{id}`
- **Authentication:** JWT Bearer token required
- **Description:** Get specific attendance details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Attendance object

### 48. Create Attendance
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

### 49. Update Attendance
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/attendances/{id}`
- **Authentication:** JWT Bearer token required (Admin, Teacher roles)
- **Description:** Update attendance details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full attendance data

### 50. Delete Attendance
- **Method:** `DELETE`
- **URL:** `/api/v1/attendances/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Delete an attendance record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Alert Management Endpoints

### 51. Get All Alerts
- **Method:** `GET`
- **URL:** `/api/v1/alerts`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student, Driver, Cleaner roles)
- **Description:** Get paginated list of alerts
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of alerts

### 52. Get Single Alert
- **Method:** `GET`
- **URL:** `/api/v1/alerts/{id}`
- **Authentication:** JWT Bearer token required
- **Description:** Get specific alert details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Alert object

### 53. Create Alert
- **Method:** `POST`
- **URL:** `/api/v1/alerts`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Driver, Cleaner roles)
- **Description:** Create a new alert
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "student_id": 1,
    "description": "Bus Delay",
    "severity": "high",
    "bus_id": 1,
    "route_id": 1
}
```

### 54. Update Alert
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/alerts/{id}`
- **Authentication:** JWT Bearer token required (Admin, Teacher roles)
- **Description:** Update alert details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full alert data
- **Possible fields:**
```json
{
    "student_id": 1,
    "description": "Updated description",
    "severity": "medium",
    "bus_id": 1,
    "route_id": 1,
    "status": "resolved"
}
```

### 55. Delete Alert
- **Method:** `DELETE`
- **URL:** `/api/v1/alerts/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Delete an alert record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Announcement Management Endpoints

### 56. Get All Announcements
- **Method:** `GET`
- **URL:** `/api/v1/announcements`
- **Authentication:** JWT Bearer token required (Admin, Teacher, Parent, Student roles)
- **Description:** Get paginated list of announcements
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of announcements

### 57. Get Single Announcement
- **Method:** `GET`
- **URL:** `/api/v1/announcements/{id}`
- **Authentication:** JWT Bearer token required
- **Description:** Get specific announcement details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Announcement object

### 58. Create Announcement
- **Method:** `POST`
- **URL:** `/api/v1/announcements`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Create a new announcement
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "title": "School Holiday",
    "description": "School will be closed on Monday for holiday",
    "audience": "all",
    "is_active": true
}
```
- **Additional Information:** The `created_by` field is automatically populated with the ID of the authenticated user.

### 59. Update Announcement
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/announcements/{id}`
- **Authentication:** JWT Bearer token required (Admin role only)
- **Description:** Update announcement details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full announcement data
- **Possible fields:**
```json
{
    "title": "Updated Title",
    "description": "Updated description",
    "audience": "students",
    "is_active": false
}
```
- **Additional Information:** Valid audience values are: all, students, parents, teachers, admin

### 60. Delete Announcement
- **Method:** `DELETE`
- **URL:** `/api/v1/announcements/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Delete an announcement record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Staff Management Endpoints

### 61. Get All Staff Profiles
- **Method:** `GET`
- **URL:** `/api/v1/staff-profiles`
- **Authentication:** JWT Bearer token required (Admin, Driver, Cleaner roles)
- **Permissions:** `view_staff`
- **Description:** Get paginated list of staff profiles
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of staff profiles

### 62. Get Single Staff Profile
- **Method:** `GET`
- **URL:** `/api/v1/staff-profiles/{id}`
- **Authentication:** JWT Bearer token required (Admin, Driver, Cleaner roles)
- **Permissions:** `view_staff`
- **Description:** Get specific staff profile details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Staff profile object

### 63. Create Staff Profile
- **Method:** `POST`
- **URL:** `/api/v1/staff-profiles`
- **Authentication:** JWT Bearer token required (Admin role)
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

### 64. Update Staff Profile
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/staff-profiles/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Permissions:** `edit_staff`
- **Description:** Update staff profile details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full staff profile data

### 65. Delete Staff Profile
- **Method:** `DELETE`
- **URL:** `/api/v1/staff-profiles/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Permissions:** `delete_staff`
- **Description:** Delete a staff profile record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

## Teacher Management Endpoints

### 66. Get All Teachers
- **Method:** `GET`
- **URL:** `/api/v1/teachers`
- **Authentication:** JWT Bearer token required (Admin, Teacher roles)
- **Permissions:** `view_users`
- **Description:** Get paginated list of teachers
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of teachers

### 67. Get Single Teacher
- **Method:** `GET`
- **URL:** `/api/v1/teachers/{id}`
- **Authentication:** JWT Bearer token required (Admin, Teacher roles)
- **Permissions:** `view_users`
- **Description:** Get specific teacher details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Teacher object

### 68. Create Teacher
- **Method:** `POST`
- **URL:** `/api/v1/teachers`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Create a new teacher account
- **Headers:** `Authorization: Bearer <admin_token>`
- **Request Body:**
```json
{
    "name": "Teacher Name",
    "email": "teacher@example.com",
    "password": "password123",
    "mobile": "1234567890"
}
```
- **Response:** Success message with teacher details

### 69. Update Teacher
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/teachers/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Permissions:** `edit_users`
- **Description:** Update teacher details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full teacher data

### 70. Delete Teacher
- **Method:** `DELETE`
- **URL:** `/api/v1/teachers/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Permissions:** `delete_users`
- **Description:** Delete a teacher record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

### 71. Get My Classes (Teacher Only)
- **Method:** `GET`
- **URL:** `/api/v1/teachers/me/classes`
- **Authentication:** JWT Bearer token required (Teacher role)
- **Description:** Get classes assigned to the current teacher
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Array of classes

### 72. Get My Students (Teacher Only)
- **Method:** `GET`
- **URL:** `/api/v1/teachers/me/students`
- **Authentication:** JWT Bearer token required (Teacher role)
- **Description:** Get students in classes assigned to the current teacher
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Array of students

## Parent Management Endpoints

### 73. Get All Parents
- **Method:** `GET`
- **URL:** `/api/v1/parents`
- **Authentication:** JWT Bearer token required (Admin, Parent roles)
- **Permissions:** `view_users`
- **Description:** Get paginated list of parents
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Paginated list of parents

### 74. Get Single Parent
- **Method:** `GET`
- **URL:** `/api/v1/parents/{id}`
- **Authentication:** JWT Bearer token required (Admin, Parent roles)
- **Permissions:** `view_users`
- **Description:** Get specific parent details
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Parent object

### 75. Create Parent
- **Method:** `POST`
- **URL:** `/api/v1/parents`
- **Authentication:** JWT Bearer token required (Admin role)
- **Description:** Create a new parent account
- **Headers:** `Authorization: Bearer <admin_token>`
- **Request Body:**
```json
{
    "name": "Parent Name",
    "email": "parent@example.com",
    "password": "password123",
    "mobile": "1234567890"
}
```
- **Response:** Success message with parent details

### 76. Update Parent
- **Method:** `PUT/PATCH`
- **URL:** `/api/v1/parents/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Permissions:** `edit_users`
- **Description:** Update parent details
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:** Partial or full parent data

### 77. Delete Parent
- **Method:** `DELETE`
- **URL:** `/api/v1/parents/{id}`
- **Authentication:** JWT Bearer token required (Admin role)
- **Permissions:** `delete_users`
- **Description:** Delete a parent record
- **Headers:** `Authorization: Bearer <token>`
- **Response:** Success message

### 78. Get My Students (Parent Only)
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

- **Test Get User Info**:
  ```bash
  curl -X GET http://127.0.0.1:8000/api/v1/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
  ```

- **Test Logout**:
  ```bash
  curl -X POST http://127.0.0.1:8000/api/v1/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
  ```

#### 2. Registration Endpoint Access Tests
- **Test Registration without authentication (should fail)**:
  ```bash
  curl -X POST http://127.0.0.1:8000/api/v1/register \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password","password_confirmation":"password","role_id":2}'
  ```

- **Test Registration with non-admin token (should fail)**:
  ```bash
  curl -X POST http://127.0.0.1:8000/api/v1/register \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TEACHER_TOKEN" \
  -d '{"name":"Test User","email":"test@example.com","password":"password","password_confirmation":"password","role_id":2}'
  ```

- **Test Registration with admin token (should succeed)**:
  ```bash
  curl -X POST http://127.0.0.1:8000/api/v1/register \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -d '{"name":"New Admin User","email":"newadmin@example.com","password":"password","password_confirmation":"password","role_id":1}'
  ```

#### 3. Error Handling Tests
1. Test registration without required authentication - should return 401
2. Test registration with non-admin role - should return 403
3. Test registration with invalid data - should return 422
4. Test registration with duplicate email - should return 422

### Key Changes Summary
1. **Registration is now protected**: The `/api/v1/register` endpoint is no longer public
2. **Admin-only access**: Only users with admin role can register new users
3. **Modified response**: Admin registration returns success message instead of auto-login
4. **Enhanced security**: Prevents unauthorized user creation by unauthenticated or non-admin users

This implementation ensures that only administrators can create new user accounts, enhancing the security of the Bus Management System by preventing unauthorized registration while still allowing administrators to manage all user accounts as needed.