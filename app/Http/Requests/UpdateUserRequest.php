<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('id'); // Pull from route param

        return [
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $userId,
            'phone' => 'nullable|unique:users,phone,' . $userId,
            'nid' => 'nullable|string',
            'address' => 'nullable|string',
            'password' => 'nullable|string',
            'role' => 'nullable|string|in:customer,staff,admin',
        ];
    }
}

