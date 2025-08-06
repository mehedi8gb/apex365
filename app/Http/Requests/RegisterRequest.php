<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Apply policy if needed
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email:rfc,dns|unique:users,email|required_without:phone',
            'phone' => 'nullable|string|unique:users,phone|max:15|required_without:email',
            'password' => 'required|string|min:6',
            'nid' => ['required', 'regex:/^\d{10}$|^\d{13}$|^\d{17}$/'],
            'address' => 'required|string|max:255',
            'referralId' => 'required|string|exists:referral_codes,code',
            'transactionId' => 'required|string|exists:transactions,transactionId',
        ];
    }
}
