<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetResourceSlotsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_time' => ['date', 'sometimes'],
            'end_time' => ['date', 'sometimes'],
            'interval_type' => [Rule::in(['weekly', 'daily', 'monthly_day', 'monthly_date'])],
            'interval' => ['numeric', 'sometimes'],
            'interval_count' => ['numeric', 'sometimes'],
            'slot_start' => ['date', 'sometimes'],
            'slot_end' => ['date', 'sometimes'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
