<?php

namespace App\Http\Requests;

use App\Actions\Trip\TripIDGenerator;
use App\Models\Trip;
use Illuminate\Foundation\Http\FormRequest;

class TripStoreRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:500',
                'unique:trips,name' . ($this->trip ? ',' . $this->trip->id : '')
            ],
            'price' => ['required' , 'integer'],
            'departure_date' => ['required', 'date'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'hotel_address' => ['nullable', 'string']

        ];
    }
}
