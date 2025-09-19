<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:50', // units in Taka; min 50 taka
            'payment_method' => 'required|in:bkash,nagad,rocket',
            'mobile_number' => 'required_if:payment_method,bkash,nagad,rocket',
        ];
    }

    public function prepareForValidation(): void
    {
        // Normalize amount (remove commas etc)
        if ($this->has('amount')) {
            $this->merge(['amount' => str_replace(',', '', $this->input('amount'))]);
        }
    }
}
