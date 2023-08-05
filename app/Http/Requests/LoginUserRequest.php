<?php

namespace App\Http\Requests;

use App\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', new Password],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'validation.required',
            'email.email' => 'validation.email',
            'email.exists' => 'validation.exists',
            'password.required' => 'validation.required',
        ];
    }
}
