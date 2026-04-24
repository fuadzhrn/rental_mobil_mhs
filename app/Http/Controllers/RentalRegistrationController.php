<?php

namespace App\Http\Controllers;

use App\Models\RentalCompany;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RentalRegistrationController extends Controller
{
    /**
     * Show the form for registering a rental company.
     */
    public function show()
    {
        return view('auth.register-rental');
    }

    /**
     * Store the rental company registration.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:100', 'unique:rental_companies,company_name'],
            'email' => ['required', 'email', 'unique:rental_companies,email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // Max 5MB
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ], [
            'company_name.required' => 'Nama perusahaan harus diisi.',
            'company_name.unique' => 'Nama perusahaan sudah terdaftar.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'Nomor telepon harus diisi.',
            'address.required' => 'Alamat harus diisi.',
            'city.required' => 'Kota harus diisi.',
            'document.required' => 'Dokumen SIUP/Izin Usaha harus diunggah.',
            'document.mimes' => 'Format dokumen harus PDF, JPG, JPEG, atau PNG.',
            'document.max' => 'Ukuran dokumen maksimal 5MB.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Password konfirmasi tidak cocok.',
            'terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
        ]);

        // Upload dokumen
        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('rental-documents', 'public');
        }

        // Buat user akun untuk admin rental dengan status inactive
        $user = User::create([
            'name' => $validated['company_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => 'admin_rental',
        ]);

        // Buat rental company dengan status pending
        $rentalCompany = RentalCompany::create([
            'user_id' => $user->id,
            'company_name' => $validated['company_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'document' => $documentPath,
            'status_verification' => 'pending',
        ]);

        // TODO: Kirim notifikasi ke super admin
        // Notify super admin tentang ada rental company baru yang pending approval
        // dispatch(new NotifyNewRentalRegistration($rentalCompany));

        return redirect()->route('rental.register.success', ['company' => $rentalCompany->id])
            ->with('success', 'Pendaftaran rental company berhasil! Mohon tunggu verifikasi dari super admin.');
    }

    /**
     * Show success page after registration.
     */
    public function success($company)
    {
        $rentalCompany = RentalCompany::find($company);

        if (!$rentalCompany) {
            abort(404);
        }

        return view('rental-registration.success', compact('rentalCompany'));
    }
}
