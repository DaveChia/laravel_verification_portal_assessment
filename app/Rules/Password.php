<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Password implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //  Password length must be equal to or higher than 8
        if (strlen($value) < 8) {
            return false;
        }

        //  Password must have at least a lowercase letter a-z
        //  preg_match returns 1 if result is true, returns 0 if result is false
        if (preg_match("/[a-z]/",$value) === 0) {
            return false;
        }

        //  Password must have at least a uppercase letter A-Z
        //  preg_match returns 1 if result is true, returns 0 if result is false
        if (preg_match("/[A-Z]/",$value) === 0) {
            return false;
        }

        //  Password must have at least 1 number 0-9
        //  preg_match returns 1 if result is true, returns 0 if result is false
        if (preg_match("/[0-9]/",$value) === 0) {
            return false;
        }

        // Password must have at least 1 special character in @$!%*#?&
        //  preg_match returns 1 if result is true, returns 0 if result is false
        if (preg_match("/[@$!%*#?&]/", $value) === 0) {
            return false;
        }

        // Password is valid if all conditions above returned true
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'validation.password_format';
    }
}
