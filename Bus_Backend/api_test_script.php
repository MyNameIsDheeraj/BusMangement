<?php

// API Test script for Student Management System

$base_url = 'http://localhost:8000/api'; // Assuming Laravel is running on port 8000

// Test credentials
$credentials = [
    'email' => 'admin@example.com',
    'password' => 'password'
];

echo "Testing Student Management API...\n\n";

// Function to make HTTP requests
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, [
        'Content-Type: application/json'
    ]));
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $http_code,
        'response' => json_decode($response, true)
    ];
}

// 1. Login to get token
echo "1. Testing Login...\n";
$result = makeRequest($base_url . '/login', 'POST', $credentials);
if ($result['code'] === 200) {
    $token = $result['response']['access_token'];
    echo "Login successful. Token: " . substr($token, 0, 20) . "...\n";
    
    $auth_headers = ["Authorization: Bearer $token"];
    
    // 2. Get all students
    echo "\n2. Testing Get All Students...\n";
    $result = makeRequest($base_url . '/students', 'GET', null, $auth_headers);
    echo "Response Code: " . $result['code'] . "\n";
    if (isset($result['response']['data'])) {
        echo "Number of students: " . count($result['response']['data']) . "\n";
    }
    
    // 3. Get specific student
    if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
        $first_student_id = $result['response']['data'][0]['id'];
        echo "\n3. Testing Get Specific Student (ID: $first_student_id)...\n";
        $result = makeRequest($base_url . "/students/$first_student_id", 'GET', null, $auth_headers);
        echo "Response Code: " . $result['code'] . "\n";
        if (isset($result['response']['id'])) {
            echo "Retrieved student: " . $result['response']['user']['name'] . "\n";
        }
    }
    
    // 4. Test student search
    echo "\n4. Testing Student Search...\n";
    $result = makeRequest($base_url . "/students?search=Student", 'GET', null, $auth_headers);
    echo "Response Code: " . $result['code'] . "\n";
    
    // 5. Test students by class
    if (isset($first_student_id) && isset($result['response']['data'][0]['class_id'])) {
        $class_id = $result['response']['data'][0]['class_id'];
        echo "\n5. Testing Students by Class (Class ID: $class_id)...\n";
        $result = makeRequest($base_url . "/students/class/$class_id", 'GET', null, $auth_headers);
        echo "Response Code: " . $result['code'] . "\n";
        if (isset($result['response']) && is_array($result['response'])) {
            echo "Students in class: " . count($result['response']) . "\n";
        }
    }
    
    // 6. Test student-parent relationships
    echo "\n6. Testing Student-Parent Relationships...\n";
    $result = makeRequest($base_url . "/student-parents", 'GET', null, $auth_headers);
    echo "Response Code: " . $result['code'] . "\n";
    
    // 7. Test parent for student
    if (isset($first_student_id)) {
        echo "\n7. Testing Get Parents for Student (Student ID: $first_student_id)...\n";
        $result = makeRequest($base_url . "/student-parents/student/$first_student_id", 'GET', null, $auth_headers);
        echo "Response Code: " . $result['code'] . "\n";
    }
    
} else {
    echo "Login failed. Code: " . $result['code'] . "\n";
    print_r($result['response']);
}

echo "\nAPI testing completed.\n";