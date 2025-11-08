<?php

/**
 * @OA\Schema(
 *     schema="Student",
 *     type="object",
 *     title="Student",
 *     description="Student model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user", ref="#/components/schemas/User"),
 *     @OA\Property(property="class", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Grade 10"),
 *         @OA\Property(property="teacher", type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Mr. Smith")
 *         )
 *     ),
 *     @OA\Property(property="admission_no", type="string", example="STU001"),
 *     @OA\Property(property="address", type="string", example="123 Main Street"),
 *     @OA\Property(property="pickup_stop", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Main Gate"),
 *         @OA\Property(property="location", type="string", example="School Entrance"),
 *         @OA\Property(property="time", type="string", example="08:00")
 *     ),
 *     @OA\Property(property="drop_stop", type="object",
 *         @OA\Property(property="id", type="integer", example=2),
 *         @OA\Property(property="name", type="string", example="Main Gate"),
 *         @OA\Property(property="location", type="string", example="School Entrance"),
 *         @OA\Property(property="time", type="string", example="15:00")
 *     ),
 *     @OA\Property(property="parents", type="array",
 *         @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="mobile", type="string", example="1234567890")
 *         )
 *     ),
 *     @OA\Property(property="bus_service_active", type="boolean", example=true),
 *     @OA\Property(property="academic_year", type="string", example="2025-2026"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="mobile", type="string", example="1234567890"),
 *     @OA\Property(property="role", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="admin")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Bus",
 *     type="object",
 *     title="Bus",
 *     description="Bus model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="reg_no", type="string", example="BUS-001"),
 *     @OA\Property(property="capacity", type="integer", example=50),
 *     @OA\Property(property="driver", ref="#/components/schemas/User"),
 *     @OA\Property(property="cleaner", ref="#/components/schemas/User"),
 *     @OA\Property(property="route", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Route 1")
 *     ),
 *     @OA\Property(property="status", type="string", example="active", enum={"active", "inactive", "maintenance"}),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Route",
 *     type="object",
 *     title="Route",
 *     description="Route model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Route 1"),
 *     @OA\Property(property="description", type="string", example="School to downtown route"),
 *     @OA\Property(property="stops", type="array",
 *         @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Main Gate"),
 *             @OA\Property(property="location", type="string", example="School Entrance"),
 *             @OA\Property(property="time", type="string", example="08:00"),
 *             @OA\Property(property="order", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Stop",
 *     type="object",
 *     title="Stop",
 *     description="Stop model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Main Gate"),
 *     @OA\Property(property="location", type="string", example="School Entrance"),
 *     @OA\Property(property="time", type="string", example="08:00"),
 *     @OA\Property(property="latitude", type="number", format="double", example=40.7128),
 *     @OA\Property(property="longitude", type="number", format="double", example=-74.0060),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Payment",
 *     type="object",
 *     title="Payment",
 *     description="Payment model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="student", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="admission_no", type="string", example="STU001")
 *     ),
 *     @OA\Property(property="amount", type="number", format="double", example=100.00),
 *     @OA\Property(property="payment_method", type="string", example="card"),
 *     @OA\Property(property="transaction_id", type="string", example="txn_123456789"),
 *     @OA\Property(property="payment_date", type="string", format="date", example="2025-01-01"),
 *     @OA\Property(property="status", type="string", example="completed"),
 *     @OA\Property(property="description", type="string", example="Bus fee payment"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Attendance",
 *     type="object",
 *     title="Attendance",
 *     description="Attendance model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="student", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="admission_no", type="string", example="STU001")
 *     ),
 *     @OA\Property(property="bus", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="reg_no", type="string", example="BUS-001")
 *     ),
 *     @OA\Property(property="date", type="string", format="date", example="2025-01-01"),
 *     @OA\Property(property="status", type="string", example="present", enum={"present", "absent", "late"}),
 *     @OA\Property(property="marked_by", ref="#/components/schemas/User"),
 *     @OA\Property(property="marked_at", type="string", format="date-time", example="2025-01-01T08:00:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Alert",
 *     type="object",
 *     title="Alert",
 *     description="Alert model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Bus Delay"),
 *     @OA\Property(property="description", type="string", example="The bus is delayed by 15 minutes"),
 *     @OA\Property(property="type", type="string", example="info", enum={"info", "warning", "danger"}),
 *     @OA\Property(property="priority", type="string", example="high", enum={"low", "medium", "high"}),
 *     @OA\Property(property="student", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="admission_no", type="string", example="STU001")
 *     ),
 *     @OA\Property(property="submitted_by", ref="#/components/schemas/User"),
 *     @OA\Property(property="resolved", type="boolean", example=false),
 *     @OA\Property(property="resolved_at", type="string", format="date-time", example="2025-01-01T00:00:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Announcement",
 *     type="object",
 *     title="Announcement",
 *     description="Announcement model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="School Event"),
 *     @OA\Property(property="content", type="string", example="School will be closed on Monday"),
 *     @OA\Property(property="created_by", ref="#/components/schemas/User"),
 *     @OA\Property(property="target_roles", type="array", 
 *         @OA\Items(type="string", example="student")
 *     ),
 *     @OA\Property(property="priority", type="string", example="high", enum={"low", "medium", "high"}),
 *     @OA\Property(property="start_date", type="string", format="date", example="2025-01-01"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2025-01-02"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="StaffProfile",
 *     type="object",
 *     title="Staff Profile",
 *     description="Staff Profile model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user", ref="#/components/schemas/User"),
 *     @OA\Property(property="license_number", type="string", example="DL123456"),
 *     @OA\Property(property="salary", type="number", format="double", example=3000.00),
 *     @OA\Property(property="emergency_contact", type="string", example="Jane Smith - 1234567890"),
 *     @OA\Property(property="bus", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="reg_no", type="string", example="BUS-001")
 *     ),
 *     @OA\Property(property="hire_date", type="string", format="date-time", example="2025-01-01T00:00:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     title="Success Response",
 *     description="Standard success response format",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="data", type="object", description="Response data"),
 *     @OA\Property(property="message", type="string", example=null),
 *     @OA\Property(property="code", type="integer", example=200),
 *     @OA\Property(property="timestamp", type="string", format="date-time", example="2025-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="Error Response",
 *     description="Standard error response format",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="An error occurred"),
 *     @OA\Property(property="error", type="object", description="Error details"),
 *     @OA\Property(property="code", type="integer", example=400),
 *     @OA\Property(property="timestamp", type="string", format="date-time", example="2025-01-01T00:00:00Z")
 * )
 */