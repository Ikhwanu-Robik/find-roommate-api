<?php

namespace App\Http\Requests;

use App\Rules\PastDate;
use App\Rules\BinaryGender;
use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required',
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
