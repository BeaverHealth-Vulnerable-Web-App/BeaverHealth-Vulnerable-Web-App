<?php

namespace App\Http\Requests\Records;

use Illuminate\Foundation\Http\FormRequest;

class PatientSearchRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'patient_id' => 'required|exists:patient,patient_id',
            'keyword'    => 'nullable|string|max:255',
        ];
    }
}
