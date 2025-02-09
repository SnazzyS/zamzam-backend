<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class DiscountStoreRequest extends FormRequest
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

    public function prepareForValidation()
    {
        $invoice = Invoice::where('trip_id', request()->trip->id)
        ->where('customer_id', request()->customer->id)
        ->first();

        // dd($invoice->balance);
        if (request()->discount > $invoice->balance) {
            throw ValidationException::withMessages([
                // 'discount' => 'ޑިސްކައުން ދައްކަން ޖެހޭ ބާކީ އަށް ވެރެއް ބޮޑު'
                'message' => 'test'
            ]);
        }


    }

    public function rules(): array
    {
        return [
            'discount' => ['required', 'numeric']
        ];
    }

    public function messages(): array
    {
        return [
            'discount.required' => 'ޑިސްކައުންޓް އަދަދު ޖަހާލާ',
            'discount.numeric' => 'ޑިސްކައުންޓް އަދަދު ވާންވާނީ ނަންބަރަކަށް'
        ];
    }
}
