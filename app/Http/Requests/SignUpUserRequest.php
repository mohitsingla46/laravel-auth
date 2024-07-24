<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignUpUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'                  => 'required|max:255',
            'email'                 => 'required|unique:users|email|max:255',
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required|string|min:8'
        ];
    }
}
