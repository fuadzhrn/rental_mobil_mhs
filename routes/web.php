<?php

use App\Http\Controllers\AdminRental\DashboardController as AdminRentalDashboardController;
use App\Http\Controllers\AdminRental\BookingController as AdminRentalBookingController;
use App\Http\Controllers\AdminRental\CustomerController as AdminRentalCustomerController;
use App\Http\Controllers\AdminRental\PaymentController as AdminRentalPaymentController;
use App\Http\Controllers\AdminRental\ReviewController as AdminRentalReviewController;
use App\Http\Controllers\AdminRental\PromoController as AdminRentalPromoController;
use App\Http\Controllers\AdminRental\ReportController as AdminRentalReportController;
use App\Http\Controllers\AdminRental\VehicleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\MyBookingController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\Customer\KatalogController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SuperAdmin\ActivityLogController as SuperAdminActivityLogController;
use App\Http\Controllers\SuperAdmin\CommissionController as SuperAdminCommissionController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\RentalVerificationController as SuperAdminRentalVerificationController;
use App\Http\Controllers\SuperAdmin\ReportController as SuperAdminReportController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

Route::middleware('auth')->group(function (): void {
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

	Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
	Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
	Route::patch('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

	Route::middleware('role:super_admin')
		->prefix('super-admin')
		->name('super-admin.')
		->group(function (): void {
			Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

			Route::get('/rentals', [SuperAdminRentalVerificationController::class, 'index'])->name('rentals.index');
			Route::get('/rentals/{rentalCompany}', [SuperAdminRentalVerificationController::class, 'show'])->name('rentals.show');
			Route::patch('/rentals/{rentalCompany}/approve', [SuperAdminRentalVerificationController::class, 'approve'])->name('rentals.approve');
			Route::patch('/rentals/{rentalCompany}/reject', [SuperAdminRentalVerificationController::class, 'reject'])->name('rentals.reject');

			Route::get('/users', [SuperAdminUserController::class, 'index'])->name('users.index');
			Route::get('/users/{user}', [SuperAdminUserController::class, 'show'])->name('users.show');

			Route::get('/reports', [SuperAdminReportController::class, 'index'])->name('reports.index');
			Route::get('/reports/bookings', [SuperAdminReportController::class, 'bookings'])->name('reports.bookings');
			Route::get('/reports/payments', [SuperAdminReportController::class, 'payments'])->name('reports.payments');
			Route::get('/reports/top-vehicles', [SuperAdminReportController::class, 'topVehicles'])->name('reports.top-vehicles');
			Route::get('/reports/active-customers', [SuperAdminReportController::class, 'activeCustomers'])->name('reports.active-customers');
			Route::get('/reports/revenue', [SuperAdminReportController::class, 'revenue'])->name('reports.revenue');
			Route::get('/reports/commissions', [SuperAdminReportController::class, 'commissions'])->name('reports.commissions');
			Route::get('/commissions', [SuperAdminReportController::class, 'commissions'])->name('commissions.index');
			Route::get('/activity-logs', [SuperAdminActivityLogController::class, 'index'])->name('activity-logs.index');
		});

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

Route::middleware(['auth', 'role:customer'])
	->prefix('booking')
	->name('booking.')
	->group(function (): void {
		Route::get('{vehicle:slug}', [BookingController::class, 'create'])->name('create');
		Route::post('{vehicle:slug}', [BookingController::class, 'store'])->name('store');
	});

Route::middleware(['auth', 'role:customer'])
	->prefix('my-bookings')
	->name('customer.bookings.')
	->group(function (): void {
		Route::get('/', [MyBookingController::class, 'index'])->name('index');
		Route::get('/{booking:booking_code}', [MyBookingController::class, 'show'])->name('show');
	});

Route::middleware(['auth', 'role:customer'])->group(function (): void {
	Route::get('/my-bookings/{booking:booking_code}/review', [CustomerReviewController::class, 'create'])->name('customer.reviews.create');
	Route::post('/my-bookings/{booking:booking_code}/review', [CustomerReviewController::class, 'store'])->name('customer.reviews.store');
});

Route::middleware(['auth', 'role:customer'])
	->prefix('pembayaran')
	->name('pembayaran.')
	->group(function (): void {
		Route::get('/{booking:booking_code}', [CustomerPaymentController::class, 'show'])->name('show');
		Route::post('/{booking:booking_code}/upload', [CustomerPaymentController::class, 'uploadProof'])->name('upload');
		Route::get('/{booking:booking_code}/invoice', [CustomerPaymentController::class, 'invoice'])->name('invoice');
		Route::get('/{booking:booking_code}/bukti-transaksi', [CustomerPaymentController::class, 'receipt'])->name('receipt');
	});

Route::redirect('/pembayaran', '/katalog')->name('pembayaran.home');

Route::middleware(['auth', 'role:admin_rental'])
	->prefix('admin-rental')
	->name('admin-rental.')
	->group(function (): void {
		Route::get('/promos', [AdminRentalPromoController::class, 'index'])->name('promos.index');
		Route::get('/promos/create', [AdminRentalPromoController::class, 'create'])->name('promos.create');
		Route::post('/promos', [AdminRentalPromoController::class, 'store'])->name('promos.store');
		Route::get('/promos/{promo}/edit', [AdminRentalPromoController::class, 'edit'])->name('promos.edit');
		Route::put('/promos/{promo}', [AdminRentalPromoController::class, 'update'])->name('promos.update');
		Route::delete('/promos/{promo}', [AdminRentalPromoController::class, 'destroy'])->name('promos.destroy');
		Route::patch('/promos/{promo}/toggle', [AdminRentalPromoController::class, 'toggle'])->name('promos.toggle');

		Route::get('/customers', [AdminRentalCustomerController::class, 'index'])->name('customers.index');
		Route::get('/customers/{customer}', [AdminRentalCustomerController::class, 'show'])->name('customers.show');

		Route::get('/reviews', [AdminRentalReviewController::class, 'index'])->name('reviews.index');

		Route::get('/bookings', [AdminRentalBookingController::class, 'index'])->name('bookings.index');
		Route::get('/bookings/{booking:booking_code}', [AdminRentalBookingController::class, 'show'])->name('bookings.show');
		Route::patch('/bookings/{booking:booking_code}/mark-ongoing', [AdminRentalBookingController::class, 'markOngoing'])->name('bookings.mark-ongoing');
		Route::patch('/bookings/{booking:booking_code}/mark-completed', [AdminRentalBookingController::class, 'markCompleted'])->name('bookings.mark-completed');
		Route::patch('/bookings/{booking:booking_code}/cancel', [AdminRentalBookingController::class, 'cancel'])->name('bookings.cancel');

		Route::get('/payments', [AdminRentalPaymentController::class, 'index'])->name('payments.index');
		Route::get('/payments/{booking:booking_code}', [AdminRentalPaymentController::class, 'show'])->name('payments.show');
		Route::patch('/payments/{booking:booking_code}/verify', [AdminRentalPaymentController::class, 'verify'])->name('payments.verify');
		Route::patch('/payments/{booking:booking_code}/reject', [AdminRentalPaymentController::class, 'reject'])->name('payments.reject');

		Route::get('/reports', [AdminRentalReportController::class, 'index'])->name('reports.index');
		Route::get('/reports/bookings', [AdminRentalReportController::class, 'bookings'])->name('reports.bookings');
		Route::get('/reports/payments', [AdminRentalReportController::class, 'payments'])->name('reports.payments');
		Route::get('/reports/top-vehicles', [AdminRentalReportController::class, 'topVehicles'])->name('reports.top-vehicles');
		Route::get('/reports/active-customers', [AdminRentalReportController::class, 'activeCustomers'])->name('reports.active-customers');
		Route::get('/reports/revenue', [AdminRentalReportController::class, 'revenue'])->name('reports.revenue');
	});
