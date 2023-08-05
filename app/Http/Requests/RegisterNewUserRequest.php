<?php

namespace App\Http\Requests;

use App\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class RegisterNewUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', new Password],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'validation.required',
            'email.required' => 'validation.required',
            'email.email' => 'validation.email',
            'email.unique' => 'validation.unique',
            'password.required' => 'validation.required',
        ];
    }
}
