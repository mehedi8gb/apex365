<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminRanksRequest extends FormRequest
{
 public function authorize(): bool
    {
        // Adjust authorization logic as needed, e.g., admin-only
        return isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [];

        foreach ($this->input() as $index => $rank) {
            $id = $rank['id'] ?? null;

            $rules["$index.id"] = 'sometimes|exists:admin_rank_settings,id';
            $rules["$index.name"] = [
                'sometimes',
                'string',
                Rule::unique('admin_rank_settings', 'name')->ignore($id),
            ];
            $rules["$index.threshold"] = 'sometimes|integer|min:1';
            $rules["$index.coins"] = 'sometimes|numeric|min:0';
        }

        return $rules;
    }

    /**
     * Customize validation messages (optional).
     */
    public function messages(): array
    {
        return [
            '*.name.unique' => 'The rank name must be unique.',
            '*.threshold.min' => 'The threshold must be at least 1.',
            '*.coins.min' => 'Coins cannot be negative.',
        ];
    }
}
