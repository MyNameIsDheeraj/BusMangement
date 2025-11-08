<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }} - Bus Management API</title>
        
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f8fafc;
                color: #636b6f;
                text-align: center;
            }
            .container {
                max-width: 800px;
                margin: 100px auto;
                padding: 20px;
            }
            .title {
                font-size: 36px;
                margin-bottom: 20px;
                color: #2c3e50;
            }
            .subtitle {
                font-size: 18px;
                margin-bottom: 30px;
                color: #7f8c8d;
            }
            .api-info {
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                text-align: left;
            }
            .endpoint {
                margin: 15px 0;
                padding: 10px;
                background: #f1f8ff;
                border-left: 4px solid #3498db;
                border-radius: 4px;
            }
            .auth-info {
                margin-top: 30px;
                padding: 20px;
                background: #f9f9f9;
                border-radius: 8px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1 class="title">{{ config('app.name', 'Laravel') }}</h1>
            <div class="subtitle">Bus Management API System</div>
            
            <div class="api-info">
                <h2>API Documentation</h2>
                <p>Welcome to the Bus Management API. This is a RESTful API built with Laravel and JWT authentication.</p>
                
                <h3>Available Endpoints:</h3>
                
                <div class="endpoint">
                    <strong>Authentication:</strong><br>
                    POST /api/login - User login<br>
                    POST /api/register - User registration<br>
                    GET /api/me - Get authenticated user info<br>
                    POST /api/logout - Logout user
                </div>
                
                <div class="endpoint">
                    <strong>Users:</strong><br>
                    GET /api/users - Get all users (admin only)<br>
                    POST /api/users - Create new user (admin only)
                </div>
                
                <div class="endpoint">
                    <strong>Students:</strong><br>
                    GET /api/students - Get all students<br>
                    POST /api/students - Create new student<br>
                    GET /api/students/{id} - Get specific student<br>
                    PUT /api/students/{id} - Update student<br>
                    DELETE /api/students/{id} - Delete student
                </div>
                
                <div class="endpoint">
                    <strong>Other Resources:</strong><br>
                    Buses: /api/buses<br>
                    Routes: /api/routes<br>
                    Stops: /api/stops<br>
                    Payments: /api/payments<br>
                    Attendance: /api/attendances<br>
                    Alerts: /api/alerts<br>
                    Announcements: /api/announcements
                </div>
                
                <div class="auth-info">
                    <h3>Authentication</h3>
                    <p>Most endpoints require JWT authentication. Include the access token in the Authorization header:</p>
                    <code>Authorization: Bearer {your-jwt-token}</code>
                </div>
            </div>
            
            <div style="margin-top: 30px; color: #95a5a6; font-size: 14px;">
                <p>Laravel {{ Illuminate\Foundation\Application::VERSION }} (PHP {{ PHP_VERSION }})</p>
            </div>
        </div>
    </body>
</html>