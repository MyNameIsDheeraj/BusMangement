<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffRequest extends FormRequest
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
                    'salary' => 'required|numeric|min:0',
                    'license_number' => 'nullable|string|max:50',
                    'emergency_contact' => 'nullable|string|max:255',
                    'bus_id' => 'nullable|exists:buses,id',
                ];
                
            case 'PUT': // Update
            case 'PATCH':
                return [
                    'salary' => 'sometimes|required|numeric|min:0',
                    'license_number' => 'sometimes|nullable|string|max:50',
                    'emergency_contact' => 'sometimes|nullable|string|max:255',
                    'bus_id' => 'sometimes|nullable|exists:buses,id',
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
            'salary.required' => 'The salary is required.',
            'salary.numeric' => 'The salary must be a valid number.',
            'salary.min' => 'The salary must be at least 0.',
            'license_number.string' => 'The license number must be a string.',
            'license_number.max' => 'The license number may not be greater than 50 characters.',
            'emergency_contact.string' => 'The emergency contact must be a string.',
            'emergency_contact.max' => 'The emergency contact may not be greater than 255 characters.',
            'bus_id.exists' => 'The selected bus does not exist.',
        ];
    }
}
