<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlightStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'departure_date' => ['nullable', 'date'],
            'departure_time' => ['nullable', 'date_format:H:i'],
            'return_date' => ['nullable', 'date', 'after_or_equal:departure_date'],
            'return_time' => ['nullable', 'date_format:H:i'],
            'departure_flight_number' => ['nullable', 'string', 'max:50'],
            'departure_transit_flight_number' => ['nullable', 'string', 'max:50'],
            'return_flight_number' => ['nullable', 'string', 'max:50'],
            'return_transit_flight_number' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'return_date.after_or_equal' => 'Return date cannot be before departure date.',
        ];
    }
}
