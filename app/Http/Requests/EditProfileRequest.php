<?php

namespace App\Http\Requests;

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
            'birthdate' => [
                'sometimes',
                new PastDate,
            ],
        ];
    }
}
