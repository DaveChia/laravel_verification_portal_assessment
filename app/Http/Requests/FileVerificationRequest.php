<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileVerificationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => ['required', 'file', 'mimetypes:application/json', 'max:2000'],
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'validation.required',
            'file.file' => 'validation.file',
            'file.mimetypes' => 'validation.mimetypes',
            'file.max' => 'validation.max',
        ];
    }
}
