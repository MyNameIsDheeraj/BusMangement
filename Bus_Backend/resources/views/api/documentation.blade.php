<!DOCTYPE html>
<html>
<head>
    <title>Bus Management System API Documentation</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; }
        h2 { color: #34495e; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .endpoint { background: #f8f9fa; padding: 12px; margin: 8px 0; border-radius: 5px; border-left: 4px solid #3498db; }
        .method { display: inline-block; padding: 3px 8px; border-radius: 4px; color: white; font-size: 12px; font-weight: bold; min-width: 60px; text-align: center; }
        .get { background-color: #27ae60; }
        .post { background-color: #2980b9; }
        .put { background-color: #f39c12; }
        .patch { background-color: #9b59b6; }
        .delete { background-color: #e74c3c; }
        code { background: #eee; padding: 2px 5px; border-radius: 3px; font-family: monospace; }
        .auth-info { background: #e8f4fd; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .section { margin-bottom: 30px; }
        .subsection { margin-left: 20px; margin-top: 15px; }
        .subsection h4 { color: #7f8c8d; margin-bottom: 10px; }
        .summary { background: #e8f5e8; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .total-endpoints { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bus Management System API v1</h1>
        
        <div class="summary">
            <p><strong class="total-endpoints">Total API Endpoints: 80</strong> | Complete RESTful API with JWT authentication</p>
            <p>Bus Management System with role-based access control for Admin, Teacher, Parent, Student, Driver, and Cleaner roles.</p>
        </div>
        
        <div class="auth-info">
            <h3>Authentication</h3>
            <p>Most endpoints require JWT authentication. Include the access token in the Authorization header:</p>
            <code>Authorization: Bearer {your-jwt-token}</code>
        </div>
        
        <h2>Available Endpoints</h2>
        
        <div class="section">
            <h3>Authentication</h3>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/login</code> - User login
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/register</code> - User registration
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/me</code> - Get authenticated user info
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/logout</code> - Logout user
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/refresh</code> - Refresh JWT token
            </div>
        </div>
        
        <div class="section">
            <h3>Users (Admin only)</h3>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/users</code> - Get all users
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/users</code> - Create new user
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/users/{user}</code> - Get specific user
            </div>
            <div class="endpoint">
                <span class="method put">PUT</span> <code>/api/v1/users/{user}</code> - Update user
            </div>
            <div class="endpoint">
                <span class="method patch">PATCH</span> <code>/api/v1/users/{user}</code> - Update user (partial)
            </div>
            <div class="endpoint">
                <span class="method delete">DELETE</span> <code>/api/v1/users/{user}</code> - Delete user
            </div>
        </div>
        
        <div class="section">
            <h3>Students</h3>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/students</code> - Get all students
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/students</code> - Create new student
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/students/{id}</code> - Get specific student
            </div>
            <div class="endpoint">
                <span class="method put">PUT</span> <code>/api/v1/students/{id}</code> - Update student
            </div>
            <div class="endpoint">
                <span class="method patch">PATCH</span> <code>/api/v1/students/{id}</code> - Update student (partial)
            </div>
            <div class="endpoint">
                <span class="method delete">DELETE</span> <code>/api/v1/students/{id}</code> - Delete student
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/students/bulk-delete</code> - Bulk delete students
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/students/class/{classId}</code> - Get students by class
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/students/{studentId}/assign-parent</code> - Assign parent to student
            </div>
            <div class="endpoint">
                <span class="method delete">DELETE</span> <code>/api/v1/students/{studentId}/remove-parent/{parentId}</code> - Remove parent from student
            </div>
        </div>
        
        <div class="section">
            <h3>Student-Parent Relationships</h3>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/student-parents</code> - Get all student-parent relationships
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/student-parents</code> - Create student-parent relationship
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/student-parents/{id}</code> - Get specific relationship
            </div>
            <div class="endpoint">
                <span class="method delete">DELETE</span> <code>/api/v1/student-parents/{id}</code> - Delete relationship
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/student-parents/student/{studentId}</code> - Get parents for student
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/student-parents/parent/{parentId}</code> - Get students for parent
            </div>
        </div>
        
        <div class="section">
            <h3>Teachers</h3>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/teachers</code> - Get all teachers
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/teachers</code> - Create new teacher
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/teachers/{id}</code> - Get specific teacher
            </div>
            <div class="endpoint">
                <span class="method put">PUT</span> <code>/api/v1/teachers/{id}</code> - Update teacher
            </div>
            <div class="endpoint">
                <span class="method patch">PATCH</span> <code>/api/v1/teachers/{id}</code> - Update teacher (partial)
            </div>
            <div class="endpoint">
                <span class="method delete">DELETE</span> <code>/api/v1/teachers/{id}</code> - Delete teacher
            </div>
            <div class="subsection">
                <h4>Teacher-specific endpoints</h4>
                <div class="endpoint">
                    <span class="method get">GET</span> <code>/api/v1/teachers/me/classes</code> - Get my classes
                </div>
                <div class="endpoint">
                    <span class="method get">GET</span> <code>/api/v1/teachers/me/students</code> - Get my students
                </div>
            </div>
        </div>
        
        <div class="section">
            <h3>Parents</h3>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/parents</code> - Get all parents
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/parents</code> - Create new parent
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/parents/{id}</code> - Get specific parent
            </div>
            <div class="endpoint">
                <span class="method put">PUT</span> <code>/api/v1/parents/{id}</code> - Update parent
            </div>
            <div class="endpoint">
                <span class="method patch">PATCH</span> <code>/api/v1/parents/{id}</code> - Update parent (partial)
            </div>
            <div class="endpoint">
                <span class="method delete">DELETE</span> <code>/api/v1/parents/{id}</code> - Delete parent
            </div>
            <div class="subsection">
                <h4>Parent-specific endpoints</h4>
                <div class="endpoint">
                    <span class="method get">GET</span> <code>/api/v1/parents/me/students</code> - Get my students
                </div>
            </div>
        </div>
        
        <div class="section">
            <h3>Staff Profiles</h3>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/staff-profiles</code> - Get staff profiles (admin: all, driver/cleaner: own)
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/staff-profiles</code> - Create new staff profile (admin only)
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/staff-profiles/{id}</code> - Get specific staff profile
            </div>
            <div class="endpoint">
                <span class="method put">PUT</span> <code>/api/v1/staff-profiles/{id}</code> - Update staff profile
            </div>
            <div class="endpoint">
                <span class="method patch">PATCH</span> <code>/api/v1/staff-profiles/{id}</code> - Update staff profile (partial)
            </div>
            <div class="endpoint">
                <span class="method delete">DELETE</span> <code>/api/v1/staff-profiles/{id}</code> - Delete staff profile (admin only)
            </div>
        </div>
        
        <div class="section">
            <h3>Buses</h3>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/buses</code> - Get all buses
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/buses</code> - Create new bus
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/buses/{bus}</code> - Get specific bus
            </div>
            <div class="endpoint">
                <span class="method put">PUT</span> <code>/api/v1/buses/{bus}</code> - Update bus
            </div>
            <div class="endpoint">
                <span class="method patch">PATCH</span> <code>/api/v1/buses/{bus}</code> - Update bus (partial)
            </div>
            <div class="endpoint">
                <span class="method delete">DELETE</span> <code>/api/v1/buses/{bus}</code> - Delete bus
            </div>
        </div>
        
        <div class="section">
            <h3>Routes</h3>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/routes</code> - Get all routes
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/routes</code> - Create new route
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/routes/{route}</code> - Get specific route
            </div>
            <div class="endpoint">
                <span class="method put">PUT</span> <code>/api/v1/routes/{route}</code> - Update route
            </div>
            <div class="endpoint">
                <span class="method patch">PATCH</span> <code>/api/v1/routes/{route}</code> - Update route (partial)
            </div>
            <div class="endpoint">
                <span class="method delete">DELETE</span> <code>/api/v1/routes/{route}</code> - Delete route
            </div>
        </div>
        
        <div class="section">
            <h3>Stops</h3>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/stops</code> - Get all stops
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/stops</code> - Create new stop
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/stops/{stop}</code> - Get specific stop
            </div>
            <div class="endpoint">
                <span class="method put">PUT</span> <code>/api/v1/stops/{stop}</code> - Update stop
            </div>
            <div class="endpoint">
                <span class="method patch">PATCH</span> <code>/api/v1/stops/{stop}</code> - Update stop (partial)
            </div>
            <div class="endpoint">
                <span class="method delete">DELETE</span> <code>/api/v1/stops/{stop}</code> - Delete stop
            </div>
        </div>
        
        <div class="section">
            <h3>Payments</h3>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/payments</code> - Get all payments
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/payments</code> - Create new payment
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/payments/{payment}</code> - Get specific payment
            </div>
            <div class="endpoint">
                <span class="method put">PUT</span> <code>/api/v1/payments/{payment}</code> - Update payment
            </div>
            <div class="endpoint">
                <span class="method patch">PATCH</span> <code>/api/v1/payments/{payment}</code> - Update payment (partial)
            </div>
            <div class="endpoint">
                <span class="method delete">DELETE</span> <code>/api/v1/payments/{payment}</code> - Delete payment
            </div>
        </div>
        
        <div class="section">
            <h3>Attendances</h3>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/attendances</code> - Get all attendances
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/attendances</code> - Create new attendance
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/attendances/{attendance}</code> - Get specific attendance
            </div>
            <div class="endpoint">
                <span class="method put">PUT</span> <code>/api/v1/attendances/{attendance}</code> - Update attendance
            </div>
            <div class="endpoint">
                <span class="method patch">PATCH</span> <code>/api/v1/attendances/{attendance}</code> - Update attendance (partial)
            </div>
            <div class="endpoint">
                <span class="method delete">DELETE</span> <code>/api/v1/attendances/{attendance}</code> - Delete attendance
            </div>
        </div>
        
        <div class="section">
            <h3>Alerts</h3>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/alerts</code> - Get all alerts
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/alerts</code> - Create new alert
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/alerts/{alert}</code> - Get specific alert
            </div>
            <div class="endpoint">
                <span class="method put">PUT</span> <code>/api/v1/alerts/{alert}</code> - Update alert
            </div>
            <div class="endpoint">
                <span class="method patch">PATCH</span> <code>/api/v1/alerts/{alert}</code> - Update alert (partial)
            </div>
            <div class="endpoint">
                <span class="method delete">DELETE</span> <code>/api/v1/alerts/{alert}</code> - Delete alert
            </div>
        </div>
        
        <div class="section">
            <h3>Announcements</h3>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/announcements</code> - Get all announcements
            </div>
            <div class="endpoint">
                <span class="method post">POST</span> <code>/api/v1/announcements</code> - Create new announcement
            </div>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/announcements/{announcement}</code> - Get specific announcement
            </div>
            <div class="endpoint">
                <span class="method put">PUT</span> <code>/api/v1/announcements/{announcement}</code> - Update announcement
            </div>
            <div class="endpoint">
                <span class="method patch">PATCH</span> <code>/api/v1/announcements/{announcement}</code> - Update announcement (partial)
            </div>
            <div class="endpoint">
                <span class="method delete">DELETE</span> <code>/api/v1/announcements/{announcement}</code> - Delete announcement
            </div>
        </div>
        
        <div class="section">
            <h3>Testing</h3>
            <div class="endpoint">
                <span class="method get">GET</span> <code>/api/v1/test</code> - Test endpoint
            </div>
        </div>
        
        <h3>Interactive Documentation</h3>
        <p>For full interactive API documentation with testing capabilities, visit: <a href="/docs">Interactive API Documentation (Swagger UI)</a></p>
        
        <div class="auth-info">
            <h3>API Testing</h3>
            <p>You can test all API endpoints directly in your browser by visiting the interactive documentation. The API follows RESTful principles with proper HTTP status codes and JSON responses.</p>
        </div>
    </div>
</body>
</html>