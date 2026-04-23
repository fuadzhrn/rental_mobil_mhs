<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectRentalRequest;
use App\Models\RentalCompany;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RentalVerificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly ActivityLogService $activityLogService,
    ) {}
    public function index(Request $request): View
    {
        $request->validate([
            'status' => ['nullable', 'in:pending,approved,rejected'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $rentals = RentalCompany::query()
            ->with(['user', 'verifiedBy'])
            ->withCount(['vehicles', 'bookings'])
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('status_verification', $request->string('status')->toString());
            })
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('company_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('city', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search): void {
                            $userQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('super-admin.rentals.index', compact('rentals'));
    }

    public function show(RentalCompany $rentalCompany): View
    {
        $rentalCompany->load(['user', 'verifiedBy']);

        $vehicles = $rentalCompany->vehicles()
            ->withCount('bookings')
            ->latest('id')
            ->take(10)
            ->get();

        $totalVehicles = $rentalCompany->vehicles()->count();
        $totalBookings = $rentalCompany->bookings()->count();

        return view('super-admin.rentals.show', compact('rentalCompany', 'vehicles', 'totalVehicles', 'totalBookings'));
    }

    public function approve(RentalCompany $rentalCompany): RedirectResponse
    {
        if (!in_array($rentalCompany->status_verification, [
            RentalCompany::STATUS_PENDING,
            RentalCompany::STATUS_REJECTED,
        ], true)) {
            return back()->with('error', 'Rental ini tidak berada pada status yang dapat disetujui.');
        }

        $rentalCompany->update([
            'status_verification' => RentalCompany::STATUS_APPROVED,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'rejection_note' => null,
        ]);

        $this->notificationService->notifyUser(
            userId: (int) $rentalCompany->user_id,
            title: 'Pendaftaran Rental Disetujui',
            message: 'Pendaftaran rental "' . $rentalCompany->company_name . '" telah disetujui oleh admin super. Anda sekarang bisa mengelola kendaraan.',
            type: 'success',
            url: route('admin-rental.dashboard'),
            referenceType: 'rental_company',
            referenceId: $rentalCompany->id,
        );

        $this->activityLogService->log(
            action: 'rental.approved',
            description: 'Super admin menyetujui pendaftaran rental: ' . $rentalCompany->company_name,
            targetType: 'rental_company',
            targetId: $rentalCompany->id,
            meta: ['company_name' => $rentalCompany->company_name, 'city' => $rentalCompany->city]
        );

        return back()->with('success', 'Rental berhasil disetujui.');
    }

    public function reject(RejectRentalRequest $request, RentalCompany $rentalCompany): RedirectResponse
    {
        if (!in_array($rentalCompany->status_verification, [
            RentalCompany::STATUS_PENDING,
            RentalCompany::STATUS_APPROVED,
        ], true)) {
            return back()->with('error', 'Rental ini tidak berada pada status yang dapat ditolak.');
        }

        $validated = $request->validated();

        $rentalCompany->update([
            'status_verification' => RentalCompany::STATUS_REJECTED,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'rejection_note' => $validated['rejection_note'],
        ]);

        $this->notificationService->notifyUser(
            userId: (int) $rentalCompany->user_id,
            title: 'Pendaftaran Rental Ditolak',
            message: 'Pendaftaran rental "' . $rentalCompany->company_name . '" ditolak oleh admin super. Alasan: ' . $validated['rejection_note'],
            type: 'error',
            url: route('admin-rental.dashboard'),
            referenceType: 'rental_company',
            referenceId: $rentalCompany->id,
        );

        $this->activityLogService->log(
            action: 'rental.rejected',
            description: 'Super admin menolak pendaftaran rental: ' . $rentalCompany->company_name,
            targetType: 'rental_company',
            targetId: $rentalCompany->id,
            meta: ['company_name' => $rentalCompany->company_name, 'rejection_note' => $validated['rejection_note']]
        );

        return back()->with('success', 'Rental berhasil ditolak.');
    }
}
