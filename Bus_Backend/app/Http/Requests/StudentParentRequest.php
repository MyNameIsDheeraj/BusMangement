<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentParentRequest extends FormRequest
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
        return [
            'student_id' => 'required|exists:students,id',
            'parent_id' => 'required|exists:parents,id',
        ];
    }
    
    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'The student ID is required.',
            'student_id.exists' => 'The selected student does not exist.',
            'parent_id.required' => 'The parent ID is required.',
            'parent_id.exists' => 'The selected parent does not exist.',
        ];
    }
}