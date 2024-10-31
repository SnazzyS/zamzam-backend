<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest
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
           'national_id' => [
                'required',
                'regex:/^A\d{6}$/',
                // Rule::unique('customers')->ignore($this->route('customer'))
            ],
            'date_of_birth' => ['required', 'date'],
            'island' => ['required', 'string'],
            'phone_number' => ['required', 'integer'],
            'address' => ['required', 'string'],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'name_in_english' => ['nullable', 'string'],
            'passport_number' => ['nullable', 'string'],
            'passport_issued_date' => ['nullable', 'date'],
            'passport_expiry_date' => ['nullable', 'date', 'after:passport_issued_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_card.regex' => 'The ID number must start with A followed by 6 digits.',
            'gender.in' => 'Please select a valid gender',
            'passport_expiry_date.after' => 'The passport expirty date must be after the issued date'
        ];
    }
}
