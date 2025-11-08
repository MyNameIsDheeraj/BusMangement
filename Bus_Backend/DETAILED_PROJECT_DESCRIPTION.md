# 🚌 Bus Management System - Comprehensive Project Description

## 📋 Overview
The Bus Management System is a comprehensive Laravel-based web application designed to manage school bus operations. It provides a centralized platform for tracking student transportation, managing staff roles, handling payments, attendance, and communication between teachers, parents, and administrative staff.

## 🏗️ Technical Architecture

### Framework & Technologies
- **Framework**: Laravel 12 (PHP)
- **Authentication**: JWT (JSON Web Tokens) with tymon/jwt-auth package
- **API Documentation**: L5-Swagger for interactive API documentation
- **Database**: MySQL (with Doctrine DBAL for extended functionality)
- **Frontend Build Tools**: Vite, Tailwind CSS, Axios
- **Testing**: PHPUnit for unit and feature testing
- **API Standards**: RESTful API with consistent JSON responses

### Core Dependencies
- `php: ^8.2` - Modern PHP version support
- `laravel/framework: ^12.0` - Core Laravel framework
- `tymon/jwt-auth: ^2.2` - JWT authentication system
- `darkaonline/l5-swagger: ^9.0` - API documentation generator
- `doctrine/dbal: ^4.3` - Database abstraction layer

## 🔐 Authentication & Security

### JWT Authentication System
- **Token Management**: Access tokens with 60-minute TTL by default
- **Refresh Tokens**: 2-week window for token refresh
- **Blacklist**: Token invalidation upon logout
- **Security**: Tokens stored securely with configurable algorithms

### Security Features
- Role-based access control
- Permission-based authorization
- Token expiration and refresh mechanisms
- Rate limiting for authenticated endpoints
- Secure password hashing (bcrypt)

## 👥 User Roles & Access Control

### 1. **Admin Role** (`admin`)
- **Privileges**: Full system access
- **Permissions**: All permissions including user management, system settings, and all operational features
- **Capabilities**:
  - Create, read, update, delete all entities
  - Manage user accounts and roles
  - View all system data
  - Configure system settings
  - Access all reports and analytics

### 2. **Teacher Role** (`teacher`)
- **Privileges**: Class-specific access
- **Permissions**:
  - `view_student`: View students in assigned classes
  - `view_payment`: View payments for students in assigned classes
  - `view_bus_route`: View bus information for assigned class students
  - `view_attendance`: View attendance for assigned class students
  - `mark_attendance`: Mark attendance for assigned students
  - `view_alert`: View alerts for assigned class students
  - `create_alert`: Create alerts for assigned students
  - `view_announcement`: View system announcements
- **Capabilities**:
  - Manage students in assigned classes
  - View class-specific data
  - Create and view alerts for their students
  - Access class-specific bus information

### 3. **Parent Role** (`parent`)
- **Privileges**: Child-specific access
- **Permissions**:
  - `view_student`: View their own children's details
  - `view_payment`: View payment information for their children
  - `view_bus_route`: View bus routes for their children
  - `view_attendance`: View attendance for their children
  - `create_payment`: Make payments for their children
  - `view_alert`: View alerts for their children
  - `view_announcement`: View system announcements
- **Capabilities**:
  - View information about their own children
  - Make payments on behalf of their children
  - Access child-specific data only
  - Receive alerts and updates for their children

### 4. **Student Role** (`student`)
- **Privileges**: Self-access only
- **Permissions**:
  - `view_student`: View their own details
  - `view_payment`: View their own payment information
  - `view_bus_route`: View their assigned bus route
  - `view_attendance`: View their own attendance
  - `view_announcement`: View system announcements
- **Capabilities**:
  - Access personal information only
  - View own attendance and payment records
  - View own bus route information

### 5. **Driver Role** (`driver`)
- **Privileges**: Bus-specific access
- **Permissions**:
  - `view_bus_route`: View assigned route information
  - `view_student`: View students on assigned route
  - `view_staff`: View staff related to assigned bus
  - `mark_attendance`: Mark attendance for students on route
  - `create_alert`: Create alerts for assigned route students
  - `view_alert`: View relevant alerts
  - `view_announcement`: View system announcements
- **Capabilities**:
  - Access students on assigned bus
  - Mark attendance for assigned students
  - Create route-specific alerts

### 6. **Cleaner Role** (`cleaner`)
- **Privileges**: Bus-specific access
- **Permissions**:
  - `view_bus_route`: View assigned route information
  - `view_student`: View students on assigned route
  - `view_staff`: View staff related to assigned bus
  - `mark_attendance`: Mark attendance for students on route
  - `create_alert`: Create alerts for assigned route students
  - `view_alert`: View relevant alerts
  - `view_announcement`: View system announcements
- **Capabilities**:
  - Access students on assigned bus
  - Mark attendance for assigned students
  - Create route-specific alerts

## 📊 Core Data Models

### 1. **User Model**
- **Fields**: id, name, email, password, mobile, role_id, timestamps
- **Relationships**:
  - Belongs to Role
  - Has one ParentModel
  - Has one StaffProfile
  - Has one Student
  - Has one ClassModel (as teacher)
  - Has many buses (as driver)
  - Has many buses (as cleaner)

### 2. **Role Model**
- **Fields**: id, name, timestamps
- **Relationships**:
  - Has many Users
  - Belongs to many Permissions (many-to-many via role_has_permissions)

### 3. **Permission Model**
- **Fields**: id, name, timestamps
- **Relationships**:
  - Belongs to many Roles (many-to-many via role_has_permissions)

### 4. **ClassModel**
- **Fields**: id, class, academic_year, class_teacher_id, timestamps
- **Relationships**:
  - Belongs to User (as teacher)
  - Has many Students

### 5. **Student Model**
- **Fields**: id, user_id, class_id, admission_no, dob, address, pickup_stop_id, drop_stop_id, bus_service_active, academic_year, timestamps
- **Relationships**:
  - Belongs to User
  - Belongs to ClassModel
  - Belongs to Stop (pickup)
  - Belongs to Stop (drop)
  - Belongs to many Parents (many-to-many via student_parent)
  - Has many StudentRoute
  - Has many Payment
  - Has many BusAttendance
  - Has many Leave
  - Has many Alert

### 6. **ParentModel**
- **Fields**: id, user_id, timestamps
- **Relationships**:
  - Belongs to User
  - Belongs to many Students (many-to-many via student_parent)

### 7. **Bus Model**
- **Fields**: id, bus_number, registration_no, model, seating_capacity, status, driver_id, cleaner_id, timestamps
- **Relationships**:
  - Belongs to User (as driver)
  - Belongs to User (as cleaner)
  - Has many Route
  - Has many BusAttendance

### 8. **Route Model**
- **Fields**: id, name, bus_id, timestamps
- **Relationships**:
  - Belongs to Bus
  - Has many Stop
  - Has many BusAttendance

### 9. **Stop Model**
- **Fields**: id, name, location, route_id, timestamps
- **Relationships**:
  - Belongs to Route
  - Has many Student (as pickup)
  - Has many Student (as drop)

### 10. **Payment Model**
- **Fields**: id, student_id, amount_paid, total_amount_due, payment_type, status, payment_date, transaction_id, academic_year, timestamps
- **Relationships**:
  - Belongs to Student

### 11. **BusAttendance Model**
- **Fields**: id, student_id, bus_id, date, status, marked_by, academic_year, timestamps
- **Relationships**:
  - Belongs to Student
  - Belongs to Bus
  - Belongs to User (marked by)

### 12. **StaffProfile Model**
- **Fields**: id, user_id, salary, license_number, emergency_contact, bus_id, timestamps
- **Relationships**:
  - Belongs to User
  - Belongs to Bus

### 13. **Alert Model**
- **Fields**: id, student_id, title, description, priority, status, submitted_by, timestamps
- **Relationships**:
  - Belongs to Student
  - Belongs to User (submitted by)

### 14. **Announcement Model**
- **Fields**: id, title, content, created_by, is_active, timestamps
- **Relationships**:
  - Belongs to User (created by)

### 15. **Leave Model**
- **Fields**: id, student_id, user_id, start_date, end_date, reason, status, timestamps
- **Relationships**:
  - Belongs to Student
  - Belongs to User

### 16. **Setting Model**
- **Fields**: id, key, value, display_name, description, data_type, validation_rule, is_system_locked, is_visible, timestamps
- **Purpose**: System configuration and settings management

### 17. **StudentParent Model**
- **Fields**: student_id, parent_id, timestamps
- **Purpose**: Many-to-many relationship between students and parents

### 18. **StudentRoute Model**
- **Fields**: id, student_id, stop_id, timestamps
- **Purpose**: Relationship between students and bus stops

### 19. **BusCharge Model**
- **Fields**: id, route_id, amount, academic_year, timestamps
- **Purpose**: Bus route charges

## 🛠️ API Endpoints & Functionality

### Authentication Endpoints
- `POST /api/v1/login` - User authentication
- `POST /api/v1/register` - User registration (admin only)
- `GET /api/v1/me` - Get current user info
- `POST /api/v1/logout` - User logout
- `POST /api/v1/refresh` - Token refresh

### User Management
- `GET|POST /api/v1/users` - User list and creation (admin only)
- `GET|PUT|PATCH|DELETE /api/v1/users/{id}` - User operations (admin only)

### Student Management
- `GET|POST /api/v1/students` - Student list and creation
- `GET|PUT|PATCH|DELETE /api/v1/students/{id}` - Student operations
- `POST /api/v1/students/bulk-delete` - Delete multiple students
- `GET /api/v1/students/class/{classId}` - Students by class
- `POST /api/v1/students/{studentId}/assign-parent` - Assign parent to student
- `DELETE /api/v1/students/{studentId}/remove-parent/{parentId}` - Remove parent assignment

### Teacher-Specific Endpoints
- `GET|POST|PUT|PATCH|DELETE /api/v1/teachers` - Teacher management (admin)
- `GET /api/v1/teachers/me/classes` - Get teacher's classes
- `GET /api/v1/teachers/me/students` - Get teacher's students

### Parent-Specific Endpoints
- `GET|POST|PUT|PATCH|DELETE /api/v1/parents` - Parent management (admin)
- `GET /api/v1/parents/me/students` - Get parent's children

### Bus System
- `GET|POST|PUT|PATCH|DELETE /api/v1/buses` - Bus management
- `GET|POST|PUT|PATCH|DELETE /api/v1/routes` - Route management
- `GET|POST|PUT|PATCH|DELETE /api/v1/stops` - Stop management

### Financial Management
- `GET|POST|PUT|PATCH|DELETE /api/v1/payments` - Payment management

### Attendance & Tracking
- `GET|POST|PUT|PATCH|DELETE /api/v1/attendances` - Attendance tracking

### Communication System
- `GET|POST|PUT|PATCH|DELETE /api/v1/alerts` - Alert/Notification system
- `GET|POST|PUT|PATCH|DELETE /api/v1/announcements` - System announcements

### Staff Management
- `GET|POST|PUT|PATCH|DELETE /api/v1/staff-profiles` - Staff profile management

### Student-Parent Relationships
- `GET|POST|DELETE /api/v1/student-parents` - Manage student-parent relationships
- `GET /api/v1/student-parents/student/{studentId}` - Get parents for student
- `GET /api/v1/student-parents/parent/{parentId}` - Get students for parent

## 🔍 Role-Based Access Control (RBAC) Implementation

### Access Patterns
1. **Admin Access**: Full CRUD permissions across all entities
2. **Teacher Access**: 
   - View students in their assigned classes only
   - View payments for students in their classes
   - Access buses related to their students
   - Create/update alerts for their students
3. **Parent Access**:
   - View only their own children's information
   - Access payments for their children
   - Access bus information for their children
4. **Driver/Cleaner Access**:
   - Access students on their assigned bus routes
   - Mark attendance for students on their routes
5. **Student Access**:
   - Self-view only for personal information

### Permission System
- **Granular Permissions**: 34 different permissions defined
- **Permission Assignment**: Each role gets specific permissions based on their needs
- **Middleware**: RoleMiddleware and PermissionMiddleware for access control
- **Dynamic Assignment**: Permissions can be modified without code changes

## 📈 Data Relationships

### Key Relationships
- **Users ↔ Roles**: Many-to-one (each user has one role)
- **Roles ↔ Permissions**: Many-to-many (complex permission matrix)
- **Classes ↔ Students**: One-to-many (one class has many students)
- **Students ↔ Parents**: Many-to-many (student_parent pivot table)
- **Students ↔ Stops**: Many-to-many (student_route pivot table)
- **Buses ↔ Routes**: One-to-many (one bus has one route, but routes can change)
- **Routes ↔ Stops**: One-to-many (route has multiple stops)
- **Students ↔ Payments**: One-to-many (student makes multiple payments)
- **Students ↔ Attendance**: One-to-many (attendance records over time)

## 🛠️ System Configuration

### Settings Management
- **Academic Year**: Configurable current academic year
- **Bus Service**: Toggle for bus service availability
- **Payment Due Date**: Configurable due date for payments
- **Late Fee Percentage**: Configurable late payment fees
- **Admin Email**: System contact information
- **Timezone**: Configurable system timezone

### Configuration Features
- **System-Locked Settings**: Uneditable settings that are system-critical
- **Visibility Control**: Some settings visible to users, others admin-only
- **Data Type Validation**: String, boolean, integer, decimal data types
- **Validation Rules**: Configurable validation for settings

## 🧪 Testing Framework

### Test Coverage
- **Authentication Tests**: Login, logout, refresh token functionality
- **User Management Tests**: CRUD operations with role-based access
- **Role-Specific Access Tests**: Teacher, parent, driver, cleaner access patterns
- **End-to-End Tests**: Comprehensive role-based access verification
- **API Contract Tests**: Endpoint response structure validation
- **Permission Tests**: Detailed permission checking for each role

### Test Categories
- **Unit Tests**: Individual method testing
- **Feature Tests**: End-to-end API flow testing
- **Integration Tests**: Cross-module functionality testing
- **Security Tests**: Access control validation

## 🚀 Deployment & Setup

### Setup Commands
```bash
composer install
npm install
php artisan key:generate
php artisan jwt:secret
php artisan migrate --force
npm run build
```

### Development Commands
```bash
npm run dev  # Development server
php artisan serve  # Laravel development server
php artisan test  # Run tests
```

### Environment Configuration
- **JWT Configuration**: Secure token generation and validation
- **Database Configuration**: Multi-database support
- **File Storage**: Configurable file system settings
- **Caching**: Redis/Memcached support

## 💡 Business Logic Features

### Student Management
- **Admission Tracking**: Student admission numbers and academic years
- **Route Assignment**: Pickup and drop-off stops management
- **Bus Service**: Enable/disable bus service for individual students
- **Parent Linking**: Multiple parents can be linked to one or more students

### Transportation Management
- **Bus Assignment**: Drivers and cleaners assigned to specific buses
- **Route Planning**: Bus routes with multiple stops
- **Student Routing**: Students assigned to specific stops on routes
- **Capacity Management**: Bus seating capacity tracking

### Financial Management
- **Payment Types**: Monthly, quarterly, and semi-annual payment options
- **Payment Tracking**: Detailed payment records with status tracking
- **Transaction IDs**: Unique transaction identification
- **Academic Year Tracking**: Payment records organized by academic year

### Attendance System
- **Daily Tracking**: Bus attendance recorded by date
- **Status Management**: Present/absent status tracking
- **Marking Authority**: Teachers, drivers, cleaners can mark attendance
- **Academic Year Grouping**: Attendance grouped by academic year

### Communication System
- **Alerts**: Priority-based alert system for students
- **Announcements**: System-wide announcements
- **Staff Communication**: Teachers, drivers, cleaners can create alerts
- **Parent Notifications**: Parents can view alerts for their children

## 📊 Reporting & Analytics

### Built-in Reports
- **Student Roster**: Class-wise student lists
- **Payment Reports**: Payment status by student/class
- **Attendance Reports**: Daily/monthly attendance analytics
- **Bus Utilization**: Bus usage and capacity reports
- **Financial Reports**: Payment collection and due reports

### Access Control in Reporting
- **Role-Specific Reports**: Each role sees only relevant data
- **Filtering Options**: Reports can be filtered by class, date, status
- **Export Capabilities**: Data export functionality planned
- **Dashboard Views**: Role-specific dashboard information

## 🔧 Maintenance & Administration

### Admin Functions
- **User Management**: Create, update, delete users
- **Role Management**: Assign roles and permissions
- **System Settings**: Configure global system parameters
- **Data Migration**: Database schema updates and maintenance
- **Backup Management**: System backup and recovery

### System Monitoring
- **API Logging**: Request/response logging
- **Error Tracking**: Comprehensive error handling
- **Performance Monitoring**: API response time tracking
- **Security Monitoring**: Authentication and authorization logging

## 🌐 Integration Capabilities

### External System Integration
- **SMS Notifications**: Text message alerts for parents
- **Email Systems**: Automated email notifications
- **Mobile Apps**: Mobile application compatibility
- **Third-party Systems**: Integration with school management systems

### API Standards
- **RESTful Design**: Consistent REST architecture
- **JSON Format**: Standardized JSON responses
- **HTTP Status Codes**: Proper HTTP status code usage
- **Error Handling**: Consistent error response format

## 📱 Mobile Compatibility

### API Design
- **Mobile-First**: Optimized for mobile applications
- **Lightweight Responses**: Efficient data transfer
- **Caching Support**: Efficient caching headers
- **Bandwidth Optimization**: Minimal data transfer for mobile networks

### Feature Support
- **Offline Mode**: Planned offline functionality
- **Push Notifications**: Real-time notification support
- **Mobile UI Integration**: Supports native mobile applications
- **Responsive Design**: Mobile-friendly API responses

## 🛡️ Security Features

### Authentication Security
- **JWT Encryption**: Secure token encryption
- **Token Expiration**: Automatic token expiration
- **Blacklisting**: Logout invalidates tokens
- **Rate Limiting**: Protection against brute force attacks

### Data Security
- **Access Control**: Granular permission system
- **Data Isolation**: Role-based data visibility
- **Input Validation**: Comprehensive input validation
- **SQL Injection Prevention**: Laravel's built-in protection

### Security Compliance
- **Data Privacy**: Role-based data access
- **Audit Trail**: Activity logging
- **Secure Communication**: HTTPS support
- **Password Security**: Bcrypt hashing

This comprehensive Bus Management System provides a robust, scalable solution for managing school transportation with advanced role-based access control, comprehensive reporting, and secure authentication systems.