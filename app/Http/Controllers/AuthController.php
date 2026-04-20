<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(request()->user()->role);
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole($request->user()->role);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = $request->user();

        return $this->redirectByRole($user->role, $request);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function redirectByRole(string $role, ?Request $request = null): RedirectResponse
    {
        if ($role === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }

        if ($role === 'admin_rental') {
            return redirect()->route('admin-rental.dashboard');
        }

        if ($request) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Role akun tidak dikenali. Hubungi administrator.',
            ]);
        }

        return redirect()->route('home');
    }
}
