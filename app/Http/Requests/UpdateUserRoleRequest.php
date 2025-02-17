<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:user,user_id',
            'role' => ['required', 'string', Rule::in($this->validRoles())],
            'value' => 'required|boolean',
        ];
    }

    private function validRoles(): array
    {
        return [
            'is_admin',
            'request_records',
            'load_records',
            'view_patient_info',
        ];
    }
}
