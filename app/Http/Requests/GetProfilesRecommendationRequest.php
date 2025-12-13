<?php

namespace App\Http\Requests;

use App\Rules\BinaryGender;
use Illuminate\Foundation\Http\FormRequest;

class GetProfilesRecommendationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('min_age')) {
            $this->merge([
                'min_birthdate' => $this->getBirthdateWhereAge((int) $this->min_age),
            ]);
        }
        if ($this->has('max_age')) {
            $this->merge([
                'max_birthdate' => $this->getBirthdateWhereAge((int) $this->max_age),
            ]);
        }
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
                'exclude_without:min_age',  // 'exclude' stops the validation process for current field
                                            // so 'exclude' must be above 'gte' because 'gte' will fail
                                            // if 'min_age' field is not present
                'integer',
                'gte:min_age',
            ],
            'min_birthdate' => [
                'required_with:min_age',
                'date'
            ],
            'max_birthdate' => [
                'required_with:max_age',
                'date'
            ],
            'lodging_id' => [
                'required',
                'exists:lodgings,id'
            ],
            'bio' => 'required',
        ];
    }

    private function getBirthdateWhereAge(int $age)
    {
        $birthdate = now()->subYears($age);
        return $birthdate->toDateString();
    }
}
