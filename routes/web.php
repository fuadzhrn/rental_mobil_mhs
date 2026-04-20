<?php

use App\Http\Controllers\AdminRental\DashboardController as AdminRentalDashboardController;
use App\Http\Controllers\AdminRental\VehicleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\KatalogController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

Route::middleware('auth')->group(function (): void {
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

	Route::get('/super-admin/dashboard', [SuperAdminDashboardController::class, 'index'])
		->middleware('role:super_admin')
		->name('super-admin.dashboard');

	Route::middleware('role:admin_rental')
		->prefix('admin-rental')
		->name('admin-rental.')
		->group(function (): void {
			Route::get('/dashboard', [AdminRentalDashboardController::class, 'index'])->name('dashboard');
			Route::resource('vehicles', VehicleController::class)->except(['show']);
			Route::delete('/vehicles/gallery/{image}', [VehicleController::class, 'destroyGalleryImage'])->name('vehicles.gallery.destroy');
		});
});

Route::view('/', 'home.index')->name('home');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{vehicle:slug}', [KatalogController::class, 'show'])->name('katalog.show');
Route::view('/booking', 'booking.index')->name('booking');
Route::view('/pembayaran', 'pembayaran.index')->name('pembayaran');
Route::view('/pembayaran/invoice', 'pembayaran.invoice-dummy')->name('pembayaran.invoice');
Route::view('/pembayaran/cetak-bukti', 'pembayaran.cetak-bukti')->name('pembayaran.cetak');
