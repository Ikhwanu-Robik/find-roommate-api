<?php

namespace App\Http\Requests\Auth;

use App\Rules\IndonesianPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'phone' => [
                'required',
                new IndonesianPhoneNumber,
                'unique:users,phone'
            ],
            'password' => 'required'
        ];
    }
}
