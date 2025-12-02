<?php

namespace App\Http\Requests;

use App\Rules\BinaryGender;
use Illuminate\Foundation\Http\FormRequest;

class GetMatchingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gender' => [
                'required',
                new BinaryGender
            ],
            'age' => [
                'required',
                'integer',
                'min:17'
            ],
            'address' => 'required',
            'lodging_id' => [
                'required',
                'exists:lodgings,id'
            ]
        ];
    }
}
