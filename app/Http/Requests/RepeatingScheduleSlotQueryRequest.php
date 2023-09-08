<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RepeatingScheduleSlotQueryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date'],
            'end_date' => ['sometimes', 'date'],
            'interval_type' => [Rule::in(['weekly', 'daily', 'monthly-day', 'monthly-date'])],
            'interval' => ['numeric', 'sometimes'],
            'weekly_days' => ['array'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'weekly_days' => $this->input('weekly_days') ? explode(',', $this->input('weekly_days')) : [],
        ]);
    }
}
