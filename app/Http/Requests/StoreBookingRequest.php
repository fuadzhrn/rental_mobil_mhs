<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_address' => ['required', 'string', 'max:500'],
            'identity_number' => ['required', 'string', 'max:50'],
            'driver_license_number' => ['nullable', 'string', 'max:50'],
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['required', 'date', 'after_or_equal:pickup_date'],
            'pickup_time' => ['nullable', 'date_format:H:i'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'return_location' => ['nullable', 'string', 'max:255'],
            'with_driver' => ['nullable', 'boolean'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama lengkap wajib diisi.',
            'customer_email.required' => 'Email wajib diisi.',
            'customer_email.email' => 'Format email tidak valid.',
            'customer_phone.required' => 'Nomor HP wajib diisi.',
            'customer_address.required' => 'Alamat wajib diisi.',
            'identity_number.required' => 'Nomor KTP wajib diisi.',
            'pickup_date.required' => 'Tanggal mulai sewa wajib diisi.',
            'pickup_date.after_or_equal' => 'Tanggal mulai sewa tidak boleh di masa lalu.',
            'return_date.required' => 'Tanggal selesai sewa wajib diisi.',
            'return_date.after_or_equal' => 'Tanggal selesai sewa tidak boleh lebih kecil dari tanggal mulai sewa.',
            'pickup_location.required' => 'Lokasi pengambilan wajib diisi.',
            'pickup_time.date_format' => 'Format jam pengambilan tidak valid.',
            'with_driver.boolean' => 'Pilihan driver tidak valid.',
            'promo_code.max' => 'Kode promo maksimal 50 karakter.',
        ];
    }
}
