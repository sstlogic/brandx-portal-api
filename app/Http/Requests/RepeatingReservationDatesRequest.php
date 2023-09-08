<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RepeatingReservationDatesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
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
