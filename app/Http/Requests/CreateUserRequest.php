<?php

namespace App\Http\Requests;

use App\Rules\UniqueEmailRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', new UniqueEmailRule],
            'first_name' => ['required', 'string'],
            'last_name' => ['sometimes', 'nullable', 'string'],
            'password' => ['required', 'string', Password::default()],

            'phone' => ['sometimes', 'string', 'nullable'],
            'type' => [Rule::in(['individual', 'organisation'])],
            'organisation_name' => ['sometimes', 'string', 'nullable', Rule::requiredIf($this->isForOrganisation())],
            'organisation_type' => ['sometimes', 'string', 'nullable', Rule::requiredIf($this->isForOrganisation())],
            'organisation_abn' => ['sometimes', 'string', 'nullable', Rule::requiredIf($this->isForOrganisation())],
            'artform' => ['string', 'nullable'],
            'address' => ['string', 'nullable'],
            'suburb' => ['string', 'nullable'],
            'state' => ['string', 'nullable'],
            'postcode' => ['string', 'nullable'],
            'country' => ['string', 'nullable'],
            'tcs' => ['required', 'boolean'],
            'updates' => ['filled', 'boolean'],
            'insurance' => ['filled', 'string'],

            'account_type' => ['string', 'nullable'],
            'hear_from_us' => ['string', 'nullable'],
            'role_in_org' => ['string', 'nullable'],
            'accurate' => ['string', 'nullable'],
            'website' => ['string', 'nullable'],

            // 'phone' => ['sometimes', 'string', 'nullable'],
            // 'type' => [Rule::in(['individual', 'organisation'])],
            // 'organisation_name' => ['sometimes', 'string', 'nullable', Rule::requiredIf($this->isForOrganisation())],
            // 'organisation_type' => ['sometimes', 'string', 'nullable', Rule::requiredIf($this->isForOrganisation())],
            // 'organisation_abn' => ['sometimes', 'string', 'nullable', Rule::requiredIf($this->isForOrganisation())],
            // 'artform' => ['required', 'string', 'nullable'],
            // 'address' => ['required', 'string'],
            // 'suburb' => ['required', 'string'],
            // 'state' => ['required', 'string'],
            // 'postcode' => ['required', 'string'],
            // 'country' => ['required', 'string'],
            // 'tcs' => ['required', 'boolean'],
            // 'updates' => ['filled', 'boolean'],
            // 'insurance' => ['filled', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    private function isForOrganisation(): bool
    {
        return $this->input('type') === 'organisation';
    }
}