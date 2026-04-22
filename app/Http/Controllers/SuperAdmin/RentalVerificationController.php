<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectRentalRequest;
use App\Models\RentalCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RentalVerificationController extends Controller
{
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

        $rentalCompany->update([
            'status_verification' => RentalCompany::STATUS_REJECTED,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'rejection_note' => $request->string('rejection_note')->toString(),
        ]);

        return back()->with('success', 'Rental berhasil ditolak.');
    }
}
