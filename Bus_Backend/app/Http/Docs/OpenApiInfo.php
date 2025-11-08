<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Bus Management System API Documentation",
 *     description="API documentation for the Bus Management System with role-based access and JWT authentication",
 *     @OA\Contact(
 *         name="API Support",
 *         email="support@busmanagement.com",
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * ),
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api/v1",
 *     description="Bus Management API Server"
 * ),
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */