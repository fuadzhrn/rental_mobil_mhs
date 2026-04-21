<?php

namespace App\Http\Controllers\AdminRental;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePromoRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Models\Promo;
use App\Models\RentalCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PromoController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()->route('admin-rental.dashboard')->with('error', 'Akun admin rental ini belum memiliki rental company.');
        }

        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $promos = Promo::query()
            ->where('rental_company_id', $rentalCompany->id)
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('promo_code', 'like', '%' . strtoupper($search) . '%');
                });
            })
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('status', $request->string('status')->toString());
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin-rental.promos.index', compact('promos', 'rentalCompany'));
    }

    public function create(): View|RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()->route('admin-rental.dashboard')->with('error', 'Akun admin rental ini belum memiliki rental company.');
        }

        $promo = new Promo([
            'discount_type' => Promo::DISCOUNT_PERCENT,
            'status' => Promo::STATUS_ACTIVE,
            'min_transaction' => 0,
            'loyal_only' => false,
        ]);

        return view('admin-rental.promos.create', compact('promo', 'rentalCompany'));
    }

    public function store(StorePromoRequest $request): RedirectResponse
    {
        $rentalCompany = $this->getRentalCompanyOrAbort();
        $validated = $request->validated();

        Promo::create([
            'rental_company_id' => $rentalCompany->id,
            'title' => $validated['title'],
            'promo_code' => strtoupper($validated['promo_code']),
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_transaction' => $validated['min_transaction'] ?? 0,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'quota' => $validated['quota'] ?? null,
            'used_count' => 0,
            'loyal_only' => $request->boolean('loyal_only'),
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin-rental.promos.index')->with('success', 'Promo berhasil ditambahkan.');
    }

    public function edit(Promo $promo): View
    {
        $rentalCompany = $this->getRentalCompanyOrAbort();
        $this->ensurePromoBelongsToRental($promo, $rentalCompany->id);

        return view('admin-rental.promos.edit', compact('promo', 'rentalCompany'));
    }

    public function update(UpdatePromoRequest $request, Promo $promo): RedirectResponse
    {
        $rentalCompany = $this->getRentalCompanyOrAbort();
        $this->ensurePromoBelongsToRental($promo, $rentalCompany->id);

        $validated = $request->validated();

        $promo->update([
            'title' => $validated['title'],
            'promo_code' => strtoupper($validated['promo_code']),
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_transaction' => $validated['min_transaction'] ?? 0,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'quota' => $validated['quota'] ?? null,
            'loyal_only' => $request->boolean('loyal_only'),
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin-rental.promos.index')->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroy(Promo $promo): RedirectResponse
    {
        $rentalCompany = $this->getRentalCompanyOrAbort();
        $this->ensurePromoBelongsToRental($promo, $rentalCompany->id);

        $promo->delete();

        return redirect()->route('admin-rental.promos.index')->with('success', 'Promo berhasil dihapus.');
    }

    public function toggle(Promo $promo): RedirectResponse
    {
        $rentalCompany = $this->getRentalCompanyOrAbort();
        $this->ensurePromoBelongsToRental($promo, $rentalCompany->id);

        $promo->update([
            'status' => $promo->status === Promo::STATUS_ACTIVE ? Promo::STATUS_INACTIVE : Promo::STATUS_ACTIVE,
        ]);

        return back()->with('success', 'Status promo berhasil diperbarui.');
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

    private function ensurePromoBelongsToRental(Promo $promo, int $rentalCompanyId): void
    {
        if ((int) $promo->rental_company_id !== $rentalCompanyId) {
            abort(404);
        }
    }
}
