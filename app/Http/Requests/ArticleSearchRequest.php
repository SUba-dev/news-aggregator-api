<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleSearchRequest extends FormRequest
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
            'keyword' => 'nullable|string',
            'fromDate' => 'nullable|date',
            'toDate' => 'nullable|date|after_or_equal:fromDate',
            'category' => 'nullable|string',
            'source' => 'nullable|string',
            'sourceOrigin' => 'nullable|string',
            'page' => 'nullable|integer',
            'perPage' => 'nullable|integer',
        ];
    }
    //


    /**
     * Custom validation error message
     */
    public function messages(): array
    {
        return [
            
        ];
    }

    /**
     * Filter the input data
     */

    public function filters()
    {
        return [
            'keyword' => 'trim|escape',
            'fromDate' => 'trim|escape',
            'toDate' => 'trim|escape',
            'category' => 'trim|escape',
            'source' => 'trim|escape',
            'perPage' => 'trim|escape',
            'page' => 'trim|escape',
        ];
    }

    
}
