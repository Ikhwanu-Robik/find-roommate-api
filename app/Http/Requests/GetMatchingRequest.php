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
            'min_age' => [
                'required',
                'integer',
                'min:17',
            ],
            'max_age' => [
                'required',
                'exclude_without:min_age', // 'exclude' stops the validation process for current field
                                             // so 'exclude' must be above 'gte' because 'gte' will fail
                                             // if 'min_age' field is not present
                'integer',
                'gte:min_age',
            ],
            'lodging_id' => [
                'required',
                'exists:lodgings,id'
            ],
            'bio' => 'required',
        ];
    }
}
