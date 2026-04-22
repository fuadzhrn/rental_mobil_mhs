<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadPaymentProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', Rule::in(array_keys(config('payment_methods')))],
            'proof_payment' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
            'proof_payment.required' => 'Bukti pembayaran wajib diupload.',
            'proof_payment.file' => 'Bukti pembayaran harus berupa file yang valid.',
            'proof_payment.mimes' => 'Bukti pembayaran harus berformat JPG, PNG, JPEG, atau PDF.',
            'proof_payment.mimetypes' => 'Tipe file bukti pembayaran tidak valid.',
            'proof_payment.max' => 'Ukuran file bukti pembayaran maksimal 5MB.',
        ];
    }
}
