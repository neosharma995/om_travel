<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'travel_date' => ['required', 'date', 'after_or_equal:today'],
            'guests' => ['required', 'integer', 'min:1', 'max:30'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
