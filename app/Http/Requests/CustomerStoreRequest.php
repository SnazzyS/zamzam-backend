<?php

namespace App\Http\Requests;

use App\Constants\ErrorMessages;
use Error;
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
           'national_id' => array_filter([
                'required',
                'regex:/^A\d{6}$/',
                // Only enforce unique on update, store uses firstOrCreate to handle existing customers
                $this->route('customer') ? Rule::unique('customers')->ignore($this->route('customer')) : null,
            ]),
            'date_of_birth' => ['required', 'date', 'before:today'],
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
            'name.required' => ErrorMessages::NAME_REQUIRED,
            'name.string' => ErrorMessages::STRING,
            'name.max' => ErrorMessages::MAX_CHARACTER,
            
            'national_id.required' => ErrorMessages::NATIONAL_ID_REQUIRED,
            'national_id.regex' => ErrorMessages::NATIONAL_ID_REGEX,
            'national_id.unique' => ErrorMessages::NATIONAL_ID_UNIQUE,
            
            'date_of_birth.required' => ErrorMessages::DATE_OF_BIRTH_REQUIRED,
            'date_of_birth.date' => ErrorMessages::DATE_OF_BIRTH_DATE,
            'date_of_birth.before' => ErrorMessages::DATE_OF_BIRTH_BEFORE,
            
            'island.required' => ErrorMessages::ISLAND_REQUIRED,
            'island.string' => ErrorMessages::STRING,
            'island.max' => ErrorMessages::MAX_CHARACTER,
            
            'phone_number.required' => ErrorMessages::PHONE_NUMBER,
            'phone_number.integer' => ErrorMessages::INTEGER,
            'phone_number.digits_between' => ErrorMessages::PHONE_NUMBER_LENGTH,
            
            'address.required' => ErrorMessages::ADDRESS_REQUIRED,
            'address.string' => ErrorMessages::STRING,
            'address.max' => ErrorMessages::MAX_CHARACTER,
            
            'gender.required' => ErrorMessages::GENDER_REQUIRED,
            'gender.in' => ErrorMessages::GENDER_INVALID,
            
            'name_in_english.string' => ErrorMessages::NAME_ENGLISH_STRING,
            'name_in_english.max' => ErrorMessages::MAX_CHARACTER,
            
            'passport_number.string' => ErrorMessages::PASSPORT_NUMBER_STRING,
            'passport_number.max' => ErrorMessages::MAX_CHARACTER,
            
            'passport_issued_date.date' => ErrorMessages::PASSPORT_ISSUED_DATE,
            'passport_issued_date.before_or_equal' => ErrorMessages::PASSPORT_ISSUED_DATE_BEFORE,
            
            'passport_expiry_date.date' => ErrorMessages::PASSPORT_EXPIRY_DATE,
            'passport_expiry_date.after' => ErrorMessages::PASSPORT_EXPIRY_DATE_AFTER,
            
            'email.email' => ErrorMessages::EMAIL_INVALID,
            'email.max' => ErrorMessages::MAX_CHARACTER,
            
            'emergency_contact.string' => ErrorMessages::EMERGENCY_CONTACT_STRING,
            'emergency_contact.max' => ErrorMessages::MAX_CHARACTER,
            
            'emergency_phone.integer' => ErrorMessages::INTEGER,
            'emergency_phone.digits_between' => ErrorMessages::PHONE_NUMBER_LENGTH,
        ];
    }
}
