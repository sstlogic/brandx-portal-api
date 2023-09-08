<?php

namespace App\Http\Requests;

use App\Rules\CurrentPasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', new CurrentPasswordRule($this->user())],
            'password' => ['string', Password::default()],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
