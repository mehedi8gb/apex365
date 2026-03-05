<?php

namespace App\Http\Requests;

use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id; // Pull from route param

        return [
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $userId,
            'status' => ['nullable', new Enum(UserStatus::class)],
            'phone' => 'nullable|unique:users,phone,' . $userId,
            'nid' => 'nullable|string',
            'address' => 'nullable|string',
            'password' => 'nullable|string',
            'role' => 'nullable|string|in:customer,staff',
        ];
    }
}

