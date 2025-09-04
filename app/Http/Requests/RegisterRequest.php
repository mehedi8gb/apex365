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
            'phone' => [
                'required_without:email',
                'nullable',
                'string',
                'unique:users,phone',
                'regex:/^\+880\d{10}$/',
            ],

            'password' => 'required|string|min:6',
            'nid' => [
                'required',
                'regex:/^\d{10}$|^\d{13}$|^\d{17}$/',
                'unique:users,nid',
            ],
            'address' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date_format:Y-m-d',
            'referralId' => 'required|string|exists:referral_codes,code',
            'transactionId' => 'required|string|exists:transactions,transactionId',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email is already registered',
            'phone.required_without' => 'Phone is required if email is not provided',
            'phone.regex' => 'Phone must be in the format +880XXXXXXXXXX',
            'phone.unique' => 'Phone number is already registered',
            'address.required' => 'Address is required',
            'date_of_birth.date_format' => 'Date of Birth must be in the format Y-m-d',
            'password.min' => 'Password must be at least 6 characters long',
            'nid.regex' => 'NID must be 10, 13, or 17 digits long',
            'referralId.exists' => 'Referral ID does not exist',
            'transactionId.exists' => 'Transaction ID does not exist',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone Number',
            'password' => 'Password',
            'nid' => 'NID',
            'address' => 'Address',
            'date_of_birth' => 'Date of Birth',
            'referralId' => 'Referral ID',
            'transactionId' => 'Transaction ID',
        ];
    }
}
