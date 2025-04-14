<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RoomStoreRequest extends FormRequest
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
        $tripId = $this->route('trip')->id;

        return [
            'room_number' => 'integer|required|unique:rooms',
            'bed_count' => 'integer|required',
            'hotel_id'=> 'required|exists:hotels,id',

        ];
        
    }




}
