<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMentoriaSesiones extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'mentoring_description' => 'string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'mentoring_description.string' => 'La descripción debe ser un texto.',
            'mentoring_description.string' => 'La descripción no debe ser mayor a 255 caracteres.',
            // 'body.required'  => 'A message is required',
        ];
    }
}
