<?php

namespace App\Http\Requests\Auth;

use App\Rules\PastDate;
use App\Rules\BinaryGender;
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
            'password' => 'required',
            'birthdate' => [
                'required',
                new PastDate
            ],
            'gender' => [
                'required',
                new BinaryGender
            ],
            'address' => 'required',
            'bio' => 'required',
            'profile_photo' => [
                'required',
                'image'
            ]
        ];
    }
}
