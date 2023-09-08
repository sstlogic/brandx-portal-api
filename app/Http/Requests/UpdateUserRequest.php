<?php

namespace App\Http\Requests;

use App\Rules\UniqueEmailRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', new UniqueEmailRule],
            'first_name' => ['required', 'string'],
            'last_name' => ['sometimes', 'nullable', 'string'],
            'phone' => ['sometimes', 'string', 'nullable'],
            'type' => [Rule::in(['individual', 'organisation'])],
            'organisation_name' => ['sometimes', 'string', 'nullable', Rule::requiredIf($this->isForOrganisation())],
            'organisation_type' => ['sometimes', 'string', 'nullable', Rule::requiredIf($this->isForOrganisation())],
            'organisation_abn' => ['sometimes', 'string', 'nullable', Rule::requiredIf($this->isForOrganisation())],
            'artform' => ['required', 'string', 'nullable'],
            'address' => ['required', 'string'],
            'suburb' => ['required', 'string'],
            'state' => ['required', 'string'],
            'postcode' => ['required', 'string'],
            'country' => ['required', 'string'],
            'insurance' => ['filled', 'string'],
        ];
    }

    private function isForOrganisation(): bool
    {
        return $this->input('type') === 'organisation';
    }
}
