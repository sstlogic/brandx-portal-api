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
            // 'email' => ['required', 'string', 'email', new UniqueEmailRule],
            'email' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'last_name' => ['sometimes', 'nullable', 'string'],
            'wk_ph' => ['sometimes', 'string', 'nullable'],
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
            'insurance' => ['string', 'nullable'],
            //new field
            'account_type' => ['string', 'nullable'],
            'promo' => ['string', 'nullable'],
            'role_in_org' => ['string', 'nullable'],
            'accurate' => ['string', 'nullable'],
            'website' => ['string', 'nullable'],
            'update_type' => ['string', 'nullable'],
            // 'artform' => ['required', 'string', 'nullable'],
            // 'address' => ['required', 'string'],
            // 'suburb' => ['required', 'string'],
            // 'state' => ['required', 'string'],
            // 'postcode' => ['required', 'string'],
            // 'country' => ['required', 'string'],
        ];
    }

    private function isForOrganisation(): bool
    {
        return $this->input('type') === 'organisation';
    }
}
