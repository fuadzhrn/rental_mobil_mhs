<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\RentalCompany;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventories across rentals (view-only).
     */
    public function index(Request $request): View
    {
        $query = Inventory::with(['vehicle', 'rentalCompany', 'lastCheckedBy']);

        if ($request->filled('rental_company_id')) {
            $query->where('rental_company_id', $request->integer('rental_company_id'));
        }

        if ($request->filled('low_stock')) {
            // consider low stock when available <= threshold (default 1)
            $threshold = $request->integer('threshold', 1);
            $query->where('available', '<=', $threshold);
        }

        $inventories = $query->latest('id')->paginate(25)->withQueryString();

        $rentalCompanies = RentalCompany::orderBy('company_name')->get();

        return view('super-admin.inventories.index', compact('inventories', 'rentalCompanies'));
    }
}
