<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RouteRequest extends FormRequest
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
                    'description' => 'nullable|string|max:500',
                ];
                
            case 'PUT': // Update
            case 'PATCH':
                return [
                    'name' => 'sometimes|required|string|max:255',
                    'description' => 'sometimes|nullable|string|max:500',
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
            'name.required' => 'The route name is required.',
            'name.string' => 'The route name must be a string.',
            'name.max' => 'The route name may not be greater than 255 characters.',
        ];
    }
}
