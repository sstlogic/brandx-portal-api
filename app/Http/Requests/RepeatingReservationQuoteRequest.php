<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RepeatingReservationQuoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'attendees' => ['required', 'min:1', 'integer'],
            'generatingIncome' => ['required', 'boolean'],
            'funded' => ['required', 'boolean'],
            'performance' => ['required', 'boolean'],
            'start_date' => ['required', 'date'],
            'end_date' => ['sometimes', 'date'],
            'start_time' => ['required', 'date'],
            'end_time' => ['sometimes', 'date'],
            'interval_type' => [Rule::in(['weekly', 'daily', 'monthly-day', 'monthly-date'])],
            'interval' => ['numeric', 'sometimes'],
            'weekly_days' => ['array'],
        ];
    }
}
