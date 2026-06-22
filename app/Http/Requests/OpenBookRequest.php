<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OpenBookRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'font_size' => ['required', 'integer', 'min:10', 'max:40'],
        ];
    }

    public function messages(): array
    {
        return [
            'font_size.required' => 'Font Size is required!',
            'font_size.integer' => 'Font Size must be numeric!',
            'font_size.min' => 'Font Size must be greater 10!',
            'font_size.max' => 'Font Size must be less than 40!',
        ];
    }
}
