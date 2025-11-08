<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BusRequest extends FormRequest
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
                    'reg_no' => 'required|unique:buses,reg_no',
                    'capacity' => 'required|integer|min:1',
                    'model' => 'nullable|string|max:255',
                    'status' => 'required|in:active,inactive,maintenance',
                    'driver_id' => 'nullable|exists:users,id',
                    'cleaner_id' => 'nullable|exists:users,id',
                    'route_id' => 'nullable|exists:routes,id',
                ];
                
            case 'PUT': // Update
            case 'PATCH':
                return [
                    'reg_no' => 'sometimes|required|unique:buses,reg_no,' . $this->route('id'),
                    'capacity' => 'sometimes|required|integer|min:1',
                    'model' => 'sometimes|nullable|string|max:255',
                    'status' => 'sometimes|required|in:active,inactive,maintenance',
                    'driver_id' => 'sometimes|nullable|exists:users,id',
                    'cleaner_id' => 'sometimes|nullable|exists:users,id',
                    'route_id' => 'sometimes|nullable|exists:routes,id',
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
            'reg_no.required' => 'The registration number is required.',
            'reg_no.unique' => 'This registration number is already taken.',
            'capacity.required' => 'The capacity is required.',
            'capacity.min' => 'The capacity must be at least 1.',
            'status.in' => 'The status must be one of: active, inactive, maintenance.',
            'driver_id.exists' => 'The selected driver does not exist.',
            'cleaner_id.exists' => 'The selected cleaner does not exist.',
            'route_id.exists' => 'The selected route does not exist.',
        ];
    }
}
