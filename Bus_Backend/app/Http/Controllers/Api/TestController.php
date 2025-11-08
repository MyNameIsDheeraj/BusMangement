<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        return response()->json(['status' => 'success', 'message' => 'API is working correctly']);
    }
    
    public function test()
    {
        return response()->json(['status' => 'success', 'data' => 'Test endpoint working']);
    }
}
