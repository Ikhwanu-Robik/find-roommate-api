<?php

namespace App\Http\Requests;

use App\Rules\BinaryGender;
use App\Rules\PastDate;
use Illuminate\Foundation\Http\FormRequest;

class EditProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->profile->is($this->customerProfile);
    }

    public function rules(): array
    {
        return [
            'full_name' => 'sometimes',
            'gender' => [
                'sometimes',
                new BinaryGender,
            ],
            'birthdate' => [
                'sometimes',
                new PastDate,
            ],
            'address' => 'sometimes',
            'bio' => 'sometimes',
        ];
    }
}
