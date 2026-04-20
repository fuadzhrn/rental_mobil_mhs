<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . ((int) date('Y') + 1)],
            'transmission' => ['required', 'string', 'max:50'],
            'fuel_type' => ['required', 'string', 'max:50'],
            'seat_capacity' => ['required', 'integer', 'min:1', 'max:99'],
            'luggage_capacity' => ['nullable', 'integer', 'min:0', 'max:99'],
            'color' => ['nullable', 'string', 'max:50'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'terms_conditions' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,maintenance'],
            'main_image' => ['nullable', 'image', 'max:2048'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kendaraan wajib diisi.',
            'brand.required' => 'Brand wajib diisi.',
            'type.required' => 'Tipe kendaraan wajib diisi.',
            'category.required' => 'Kategori wajib diisi.',
            'year.required' => 'Tahun kendaraan wajib diisi.',
            'year.integer' => 'Tahun kendaraan harus berupa angka.',
            'transmission.required' => 'Transmisi wajib diisi.',
            'fuel_type.required' => 'Jenis bahan bakar wajib diisi.',
            'seat_capacity.required' => 'Kapasitas kursi wajib diisi.',
            'seat_capacity.integer' => 'Kapasitas kursi harus berupa angka.',
            'luggage_capacity.integer' => 'Kapasitas bagasi harus berupa angka.',
            'price_per_day.required' => 'Harga per hari wajib diisi.',
            'price_per_day.numeric' => 'Harga per hari harus berupa angka.',
            'status.required' => 'Status kendaraan wajib diisi.',
            'status.in' => 'Status kendaraan tidak valid.',
            'main_image.image' => 'Foto utama harus berupa gambar.',
            'gallery_images.array' => 'Galeri harus berupa daftar gambar.',
            'gallery_images.*.image' => 'Setiap file galeri harus berupa gambar.',
        ];
    }
}
