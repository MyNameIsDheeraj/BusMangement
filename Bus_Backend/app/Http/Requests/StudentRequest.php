<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->method();
        
        switch($method) {
            case 'POST': // Create
                return [
                    'user_id' => 'required|exists:users,id',
                    'class_id' => 'required|exists:classes,id',
                    'admission_no' => 'required|unique:students,admission_no',
                    'dob' => 'nullable|date',
                    'address' => 'nullable|string|max:500',
                    'pickup_stop_id' => 'nullable|exists:stops,id',
                    'drop_stop_id' => 'nullable|exists:stops,id',
                    'academic_year' => 'required|regex:/^\d{4}-\d{4}$/',
                    'bus_service_active' => 'sometimes|boolean',
                ];
                
            case 'PUT': // Update
            case 'PATCH':
                return [
                    'user_id' => 'sometimes|required|exists:users,id',
                    'class_id' => 'sometimes|required|exists:classes,id',
                    'admission_no' => 'sometimes|required|unique:students,admission_no,' . $this->route('id'),
                    'dob' => 'sometimes|nullable|date',
                    'address' => 'sometimes|nullable|string|max:500',
                    'pickup_stop_id' => 'sometimes|nullable|exists:stops,id',
                    'drop_stop_id' => 'sometimes|nullable|exists:stops,id',
                    'academic_year' => 'sometimes|required|regex:/^\d{4}-\d{4}$/',
                    'bus_service_active' => 'sometimes|boolean',
                ];
                
            default:
                return [];
        }
    }
    
    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'class_id.required' => 'The class ID is required.',
            'class_id.exists' => 'The selected class does not exist.',
            'admission_no.required' => 'The admission number is required.',
            'admission_no.unique' => 'This admission number is already taken.',
            'academic_year.regex' => 'Academic year must be in the format YYYY-YYYY (e.g., 2024-2025).',
        ];
    }
}