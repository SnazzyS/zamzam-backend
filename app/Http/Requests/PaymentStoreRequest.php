<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class PaymentStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function prepareForValidation()
    {

        // check if invoice status is active

        $invoiceDetails = Invoice::where('customer_id', request()->customer->id)
            ->where('trip_id', request()->trip->id)
            ->latest()
            ->first();

        if (request()->amount > $invoiceDetails->balance) {
            throw ValidationException::withMessages([
                'amount' => 'ބަލައިގަނެވޭ ފައިސާ ގިނަ'
            ]);
        }

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'transfer_reference_number' => 'numeric',
            'details' => 'string',
            'discount' => 'numeric'
        ];
    }
}
