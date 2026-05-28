<?php

namespace App\Http\Controllers\AdminRental;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Display a listing of the inventories for the logged-in rental company.
     */
    public function index()
    {
        $user = Auth::user();

        $rentalCompany = $user->rentalCompany;
        if (! $rentalCompany) {
            return redirect()->route('home')->with('warning', 'Akun belum terhubung dengan rental.');
        }

        $inventories = Inventory::with('vehicle')
            ->where('rental_company_id', $rentalCompany->id)
            ->paginate(20);

        return view('admin-rental.inventories.index', compact('inventories'));
    }

    /**
     * Update the specified inventory (e.g., adjust counts, reserve/unreserve).
     */
    public function update(Request $request, Inventory $inventory)
    {
        $user = Auth::user();

        if ($user->rentalCompany->id !== $inventory->rental_company_id) {
            abort(403);
        }

        $data = $request->validate([
            'total' => 'nullable|integer|min:0',
            'reserved' => 'nullable|integer|min:0',
            'available' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $inventory->fill($data);
        $inventory->last_checked_by = $user->id;
        $inventory->save();

        return back()->with('success', 'Inventory updated.');
    }

    /**
     * Display the specified inventory with simple edit form.
     */
    public function show(Inventory $inventory)
    {
        $user = Auth::user();

        if ($user->rentalCompany->id !== $inventory->rental_company_id) {
            abort(403);
        }

        return view('admin-rental.inventories.show', compact('inventory'));
    }
}
