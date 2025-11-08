<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParentRequest extends FormRequest
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
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|min:6',
                    'mobile' => 'nullable|string|max:15',
                ];
                
            case 'PUT': // Update
            case 'PATCH':
                $id = $this->route('id');
                return [
                    'name' => 'sometimes|required|string|max:255',
                    'email' => 'sometimes|required|email|unique:users,email,' . $id,
                    'password' => 'sometimes|required|min:6',
                    'mobile' => 'sometimes|nullable|string|max:15',
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
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email is already taken.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 6 characters.',
            'mobile.string' => 'The mobile number must be a string.',
            'mobile.max' => 'The mobile number may not be greater than 15 characters.',
        ];
    }
}
