<?php

namespace App\Http\Requests;

use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name' => 'nullable|string',
            'email' => 'nullable|email:rfc,dns|unique:users,email,' . $userId,
            'phone' => 'nullable|regex:/^\+880\d{10}$/|unique:users,phone,' . $userId,
            'nid' => [
                'nullable',
                'regex:/^\d{10}$|^\d{13}$|^\d{17}$/',
                'unique:users,nid,'. $userId,
            ],
            'address' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date_format:Y-m-d',
            'password' => 'nullable|string|confirmed'
        ];
    }
}

