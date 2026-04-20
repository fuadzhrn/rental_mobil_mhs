<?php

use App\Http\Controllers\AdminRental\DashboardController as AdminRentalDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

Route::middleware('auth')->group(function (): void {
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

	Route::get('/super-admin/dashboard', [SuperAdminDashboardController::class, 'index'])
		->middleware('role:super_admin')
		->name('super-admin.dashboard');

	Route::get('/admin-rental/dashboard', [AdminRentalDashboardController::class, 'index'])
		->middleware('role:admin_rental')
		->name('admin-rental.dashboard');
});

Route::view('/', 'home.index')->name('home');
Route::view('/katalog', 'katalog.index')->name('katalog');
Route::view('/detail-mobil', 'detail-mobil.index')->name('detail-mobil');
Route::view('/booking', 'booking.index')->name('booking');
Route::view('/pembayaran', 'pembayaran.index')->name('pembayaran');
Route::view('/pembayaran/invoice', 'pembayaran.invoice-dummy')->name('pembayaran.invoice');
Route::view('/pembayaran/cetak-bukti', 'pembayaran.cetak-bukti')->name('pembayaran.cetak');
