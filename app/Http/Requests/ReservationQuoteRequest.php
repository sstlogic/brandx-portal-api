<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationQuoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
            'attendees' => ['required', 'min:1', 'integer'],
            'generatingIncome' => ['required', 'boolean'],
            'funded' => ['required', 'boolean'],
            'performance' => ['required', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
