<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JoinListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lodging_id' => [
                'required',
                'exists:lodgings,id'
            ],
        ];
    }
}
