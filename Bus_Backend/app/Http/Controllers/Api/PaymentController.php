<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $query = Payment::with(['student.user']);
        
        // Admin can see all payments
        if ($currentUser->role->name === 'admin') {
            $payments = $query->paginate(15);
        } 
        // Teacher can see payments for students in their classes
        elseif ($currentUser->role->name === 'teacher') {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $studentIds = Student::whereIn('class_id', $classIds)->pluck('id');
            $payments = $query->whereIn('student_id', $studentIds)->paginate(15);
        } 
        // Parent can see payments for their children
        elseif ($currentUser->role->name === 'parent') {
            $studentIds = $currentUser->parent->students->pluck('id');
            $payments = $query->whereIn('student_id', $studentIds)->paginate(15);
        } 
        // Student can see their own payments
        elseif ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if (!$student) {
                return response()->json(['error' => 'Student record not found'], 404);
            }
            $payments = $query->where('student_id', $student->id)->get();
            return response()->json($payments);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Only admin and parent can create payments
        if (!in_array($currentUser->role->name, ['admin', 'parent'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'amount_paid' => 'required|numeric|min:0',
            'total_amount_due' => 'required|numeric|min:0',
            'payment_type' => 'required|in:monthly,annual',
            'status' => 'required|in:paid,pending,overdue',
            'payment_date' => 'required|date',
            'transaction_id' => 'nullable|string',
            'academic_year' => 'required|regex:/^\d{4}-\d{4}$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Check if user can create payment for this student
        if (!$this->canCreatePaymentForStudent($request->student_id)) {
            return response()->json(['error' => 'Unauthorized to create payment for this student'], 403);
        }

        $payment = Payment::create($request->all());

        return response()->json($payment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $payment = Payment::with(['student.user'])->findOrFail($id);
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if user can view this payment
        if (!$this->canViewPayment($payment)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($payment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Only admin can update payments
        if ($currentUser->role->name !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $payment = Payment::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'amount_paid' => 'sometimes|required|numeric|min:0',
            'total_amount_due' => 'sometimes|required|numeric|min:0',
            'payment_type' => 'sometimes|required|in:monthly,annual',
            'status' => 'sometimes|required|in:paid,pending,overdue',
            'payment_date' => 'sometimes|required|date',
            'transaction_id' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $payment->update($request->all());

        return response()->json($payment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Only admin can delete payments
        if ($currentUser->role->name !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully']);
    }
    
    /**
     * Check if current user can create payment for the specified student
     */
    private function canCreatePaymentForStudent($studentId)
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return false;
        }
        
        // Admins can create payments for any student
        if ($currentUser->role->name === 'admin') {
            return true;
        }
        
        // Parents can create payments for their children
        if ($currentUser->role->name === 'parent') {
            $studentIds = $currentUser->parent->students->pluck('id');
            return $studentIds->contains($studentId);
        }
        
        return false;
    }
    
    /**
     * Check if current user can view the specified payment
     */
    private function canViewPayment($payment)
    {
        $currentUser = auth('api')->user();
        
        if (!$currentUser || !$currentUser->role) {
            return false;
        }
        
        // Admins can view all payments
        if ($currentUser->role->name === 'admin') {
            return true;
        }
        
        // Teachers can view payments for students in their classes
        if ($currentUser->role->name === 'teacher') {
            $classIds = $currentUser->classTeacher ? $currentUser->classTeacher->pluck('id') : collect();
            $studentIds = Student::whereIn('class_id', $classIds)->pluck('id');
            return $studentIds->contains($payment->student_id);
        }
        
        // Parents can view payments for their children
        if ($currentUser->role->name === 'parent') {
            $studentIds = $currentUser->parent->students->pluck('id');
            return $studentIds->contains($payment->student_id);
        }
        
        // Students can view their own payments
        if ($currentUser->role->name === 'student') {
            $student = $currentUser->student;
            if (!$student) {
                return false;
            }
            return $student->id === $payment->student_id;
        }
        
        return false;
    }
}
