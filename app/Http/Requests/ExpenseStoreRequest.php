<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_id' => ['nullable', 'exists:trips,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'in:MVR,USD,SAR'],
            'category' => ['nullable', 'string', 'max:100'],
            'expense_date' => ['required', 'date'],
            'fiscal_year' => ['nullable', 'integer', 'min:2020', 'max:2100'],
            'payment_method' => ['required', 'in:transfer,cash,cheque'],
            'transfer_reference_number' => ['required_if:payment_method,transfer', 'nullable', 'string', 'max:100'],
            'cheque_number' => ['required_if:payment_method,cheque', 'nullable', 'string', 'max:100'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'transfer_reference_number.required_if' => 'Transfer reference number is required when payment method is transfer.',
            'cheque_number.required_if' => 'Cheque number is required when payment method is cheque.',
        ];
    }
}
