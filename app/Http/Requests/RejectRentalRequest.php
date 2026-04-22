<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rejection_note' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_note.required' => 'Alasan penolakan wajib diisi.',
            'rejection_note.min' => 'Alasan penolakan minimal 5 karakter.',
            'rejection_note.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ];
    }
}
