<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePendingReservationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start' => ['date', 'required'],
            'end' => ['required', 'date'],
            'user' => ['required', 'uuid', Rule::exists('users', 'uuid')],
            'resource' => ['required', 'integer'],
            'resource_name' => ['required', 'string'],
            'attendees' => ['required', 'min:1', 'integer'],
            'generatingIncome' => ['required', 'boolean'],
            'funded' => ['required', 'boolean'],
            'performance' => ['required', 'boolean'],
            'description' => ['required', 'string'],
            'interval_type' => ['sometimes', Rule::in(['weekly', 'daily', 'monthly-day', 'monthly-date'])],

            'start_time' => [Rule::requiredIf(fn () => $this->input('interval_type')), 'date'],
            'end_time' => [Rule::requiredIf(fn () => $this->input('interval_type')), 'sometimes', 'date'],

            'interval' => [Rule::requiredIf(fn () => $this->input('interval_type')), 'numeric', 'sometimes'],
            'weekly_days' => [Rule::requiredIf(fn () => $this->input('interval_type') == 'weekly'), 'array'],
        ];
    }
}
