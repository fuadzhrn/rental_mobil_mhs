<?php

namespace App\Http\Controllers\AdminRental;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelBookingRequest;
use App\Models\Booking;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\RentalCompany;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company.');
        }

        $request->validate([
            'booking_status' => ['nullable', 'in:waiting_payment,waiting_verification,confirmed,ongoing,completed,cancelled'],
            'payment_status' => ['nullable', 'in:unpaid,uploaded,verified,rejected'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $bookings = Booking::query()
            ->with(['vehicle', 'customer', 'payment'])
            ->where('rental_company_id', $rentalCompany->id)
            ->when($request->filled('booking_status'), function ($query) use ($request): void {
                $query->where('booking_status', $request->string('booking_status')->toString());
            })
            ->when($request->filled('payment_status'), function ($query) use ($request): void {
                $query->where('payment_status', $request->string('payment_status')->toString());
            })
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('booking_code', 'like', '%' . $search . '%')
                        ->orWhere('customer_name', 'like', '%' . $search . '%')
                        ->orWhereHas('vehicle', function ($vehicleQuery) use ($search): void {
                            $vehicleQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $bookingStatusOptions = Booking::statusOptions();
        $paymentStatusOptions = Booking::paymentStatusOptions();

        return view('admin-rental.bookings.index', compact('bookings', 'rentalCompany', 'bookingStatusOptions', 'paymentStatusOptions'));
    }

    public function show(Booking $booking): View|RedirectResponse
    {
        $this->authorize('view', $booking);

        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company.');
        }

        $this->ensureBookingBelongsToRental($booking, $rentalCompany->id);
        $booking->load(['customer', 'vehicle.rentalCompany', 'vehicle.primaryImage', 'vehicle.inventory', 'payment']);

        return view('admin-rental.bookings.show', compact('booking', 'rentalCompany'));
    }

    public function markOngoing(Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);
        $rentalCompany = $this->getRentalCompanyOrAbort();
        $this->ensureBookingBelongsToRental($booking, $rentalCompany->id);

        if ($booking->booking_status !== Booking::BOOKING_CONFIRMED || $booking->payment_status !== Booking::PAYMENT_VERIFIED) {
            return back()->with('error', 'Booking hanya bisa diubah ke ongoing jika status confirmed dan pembayaran verified.');
        }

        $booking->update([
            'booking_status' => Booking::BOOKING_ONGOING,
        ]);

        $this->notificationService->notifyUser(
            userId: (int) $booking->customer_id,
            title: 'Status Booking Berubah',
            message: 'Booking ' . $booking->booking_code . ' sedang berjalan (ongoing).',
            type: 'info',
            url: route('customer.bookings.show', $booking),
            referenceType: 'booking',
            referenceId: $booking->id,
        );

        $this->activityLogService->log(
            action: 'booking.marked_ongoing',
            description: 'Admin rental mengubah booking ke ongoing: ' . $booking->booking_code,
            targetType: 'booking',
            targetId: $booking->id
        );

        return back()->with('success', 'Booking berhasil diubah menjadi ongoing.');
    }

    public function confirmAvailability(Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);
        $rentalCompany = $this->getRentalCompanyOrAbort();
        $this->ensureBookingBelongsToRental($booking, $rentalCompany->id);

        if (!in_array($booking->booking_status, [Booking::BOOKING_WAITING_VERIFICATION, Booking::BOOKING_WAITING_PAYMENT, Booking::BOOKING_PENDING], true)) {
            return back()->with('error', 'Booking tidak dapat dikonfirmasi pada status saat ini.');
        }

        DB::beginTransaction();
        try {
            $inventory = Inventory::where('rental_company_id', $rentalCompany->id)
                ->where('vehicle_id', $booking->vehicle_id)
                ->lockForUpdate()
                ->first();

            if (! $inventory || ($inventory->available ?? 0) <= 0) {
                DB::rollBack();
                return back()->with('error', 'Stok tidak tersedia untuk kendaraan ini.');
            }

            // reserve one unit
            $inventory->reserved = max(0, ($inventory->reserved ?? 0) + 1);
            $inventory->available = max(0, ($inventory->available ?? 0) - 1);
            $inventory->last_checked_by = Auth::id();
            $inventory->save();

            $booking->update([
                'booking_status' => Booking::BOOKING_CONFIRMED,
            ]);

            $this->notificationService->notifyUser(
                userId: (int) $booking->customer_id,
                title: 'Booking Dikonfirmasi',
                message: 'Booking ' . $booking->booking_code . ' dikonfirmasi dan barang dipesan.',
                type: 'success',
                url: route('customer.bookings.show', $booking),
                referenceType: 'booking',
                referenceId: $booking->id,
            );

            $this->activityLogService->log(
                action: 'booking.confirmed',
                description: 'Admin rental mengkonfirmasi booking: ' . $booking->booking_code,
                targetType: 'booking',
                targetId: $booking->id
            );

            DB::commit();

            return back()->with('success', 'Booking berhasil dikonfirmasi dan stok di-reserve.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Terjadi kesalahan saat mengonfirmasi booking.');
        }
    }

    public function rejectAvailability(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);
        $rentalCompany = $this->getRentalCompanyOrAbort();
        $this->ensureBookingBelongsToRental($booking, $rentalCompany->id);

        if (!in_array($booking->booking_status, [Booking::BOOKING_WAITING_VERIFICATION, Booking::BOOKING_WAITING_PAYMENT, Booking::BOOKING_PENDING], true)) {
            return back()->with('error', 'Booking tidak dapat ditolak pada status saat ini.');
        }

        $note = $request->filled('reason') ? $request->string('reason')->toString() : null;

        DB::transaction(function () use ($booking, $note) {
            if ($booking->payment) {
                $booking->payment->update([
                    'payment_status' => Payment::STATUS_REJECTED,
                    'rejection_note' => $note ?? 'Pembayaran dibatalkan karena booking dibatalkan oleh admin.',
                ]);
            }

            $booking->update([
                'booking_status' => Booking::BOOKING_CANCELLED,
                'payment_status' => Booking::PAYMENT_REJECTED,
                'note' => $note ? trim(($booking->note ? $booking->note . PHP_EOL : '') . 'Reject reason: ' . $note) : $booking->note,
            ]);

            $this->notificationService->notifyUser(
                userId: (int) $booking->customer_id,
                title: 'Booking Dibatalkan',
                message: 'Booking ' . $booking->booking_code . ' telah dibatalkan oleh admin. Alasan: ' . ($note ?? '—'),
                type: 'warning',
                url: route('customer.bookings.show', $booking),
                referenceType: 'booking',
                referenceId: $booking->id,
            );

            $this->activityLogService->log(
                action: 'booking.cancelled',
                description: 'Admin rental membatalkan booking: ' . $booking->booking_code,
                targetType: 'booking',
                targetId: $booking->id,
                meta: ['reason' => $note]
            );
        });

        return back()->with('success', 'Booking telah dibatalkan dan pembayaran ditandai rejected.');
    }

    public function markCompleted(Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);
        $rentalCompany = $this->getRentalCompanyOrAbort();
        $this->ensureBookingBelongsToRental($booking, $rentalCompany->id);

        if ($booking->booking_status !== Booking::BOOKING_ONGOING) {
            return back()->with('error', 'Booking hanya bisa diubah ke completed jika status saat ini ongoing.');
        }

        $booking->update([
            'booking_status' => Booking::BOOKING_COMPLETED,
        ]);

        $this->notificationService->notifyUser(
            userId: (int) $booking->customer_id,
            title: 'Booking Selesai',
            message: 'Booking ' . $booking->booking_code . ' telah selesai. Anda bisa memberikan ulasan.',
            type: 'success',
            url: route('customer.bookings.show', $booking),
            referenceType: 'booking',
            referenceId: $booking->id,
        );

        $this->activityLogService->log(
            action: 'booking.marked_completed',
            description: 'Admin rental menyelesaikan booking: ' . $booking->booking_code,
            targetType: 'booking',
            targetId: $booking->id
        );

        return back()->with('success', 'Booking berhasil diselesaikan (completed).');
    }

    public function cancel(CancelBookingRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);
        $rentalCompany = $this->getRentalCompanyOrAbort();
        $this->ensureBookingBelongsToRental($booking, $rentalCompany->id);

        if (!in_array($booking->booking_status, [
            Booking::BOOKING_WAITING_PAYMENT,
            Booking::BOOKING_WAITING_VERIFICATION,
            Booking::BOOKING_CONFIRMED,
        ], true)) {
            return back()->with('error', 'Booking hanya bisa dibatalkan pada status waiting_payment, waiting_verification, atau confirmed.');
        }

        // Payment status tidak diubah pada tahap ini untuk menjaga konsistensi data pembayaran.
        $booking->update([
            'booking_status' => Booking::BOOKING_CANCELLED,
            'note' => $request->filled('cancel_reason')
                ? trim(($booking->note ? $booking->note . PHP_EOL : '') . 'Cancel reason: ' . $request->string('cancel_reason')->toString())
                : $booking->note,
        ]);

        $this->notificationService->notifyUser(
            userId: (int) $booking->customer_id,
            title: 'Booking Dibatalkan',
            message: 'Booking ' . $booking->booking_code . ' telah dibatalkan oleh admin rental.',
            type: 'warning',
            url: route('customer.bookings.show', $booking),
            referenceType: 'booking',
            referenceId: $booking->id,
        );

        $this->activityLogService->log(
            action: 'booking.cancelled',
            description: 'Admin rental membatalkan booking: ' . $booking->booking_code,
            targetType: 'booking',
            targetId: $booking->id,
            meta: ['cancel_reason' => $request->string('cancel_reason')->toString()]
        );

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    private function getRentalCompany(): ?RentalCompany
    {
        return Auth::user()?->rentalCompany;
    }

    private function getRentalCompanyOrAbort(): RentalCompany
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            abort(404);
        }

        return $rentalCompany;
    }

    private function ensureBookingBelongsToRental(Booking $booking, int $rentalCompanyId): void
    {
        if ((int) $booking->rental_company_id !== $rentalCompanyId) {
            abort(404);
        }
    }
}
