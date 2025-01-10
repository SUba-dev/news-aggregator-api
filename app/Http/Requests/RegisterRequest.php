<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255|regex:/^[a-zA-Z ]+$/',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }


    /**
     * Custom validation error message
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name is required.',
            'email.required' => 'The user email is required.',
            'email.unique' => 'This email is already taken.',
            'password.required' => 'The password is required.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }

    /**
     * Filter the input data
     */

    public function filters()
    {
        return [
            'name' => 'trim|escape',
            'email' => 'trim|escape',
        ];
    }

    
}
