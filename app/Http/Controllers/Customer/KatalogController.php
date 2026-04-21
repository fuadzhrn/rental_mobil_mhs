<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KatalogController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'transmission' => ['nullable', 'string', 'max:50'],
            'fuel_type' => ['nullable', 'string', 'max:50'],
            'seat_capacity' => ['nullable', 'integer', 'min:1', 'max:99'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'in:newest,price_low,price_high'],
        ]);

        $query = Vehicle::query()
            ->with(['rentalCompany', 'images'])
            ->visibleToCustomers();

        if (!empty($validated['q'])) {
            $search = $validated['q'];
            $query->where(function ($subQuery) use ($search): void {
                $subQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('brand', 'like', '%' . $search . '%')
                    ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        if (!empty($validated['category'])) {
            $query->where('category', $validated['category']);
        }

        if (!empty($validated['transmission'])) {
            $query->where('transmission', $validated['transmission']);
        }

        if (!empty($validated['fuel_type'])) {
            $query->where('fuel_type', $validated['fuel_type']);
        }

        if (!empty($validated['seat_capacity'])) {
            $query->where('seat_capacity', $validated['seat_capacity']);
        }

        if (!empty($validated['price_min'])) {
            $query->where('price_per_day', '>=', $validated['price_min']);
        }

        if (!empty($validated['price_max'])) {
            $query->where('price_per_day', '<=', $validated['price_max']);
        }

        $sort = $validated['sort'] ?? 'newest';

        if ($sort === 'price_low') {
            $query->orderBy('price_per_day', 'asc');
        } elseif ($sort === 'price_high') {
            $query->orderBy('price_per_day', 'desc');
        } else {
            $query->latest();
        }

        $vehicles = $query->paginate(9)->withQueryString();

        $filterSource = Vehicle::query()->visibleToCustomers();

        $categories = (clone $filterSource)->distinct()->orderBy('category')->pluck('category');
        $transmissions = (clone $filterSource)->distinct()->orderBy('transmission')->pluck('transmission');
        $fuelTypes = (clone $filterSource)->distinct()->orderBy('fuel_type')->pluck('fuel_type');
        $seatCapacities = (clone $filterSource)->distinct()->orderBy('seat_capacity')->pluck('seat_capacity');

        $summaryCount = $vehicles->total();

        return view('katalog.index', compact(
            'vehicles',
            'categories',
            'transmissions',
            'fuelTypes',
            'seatCapacities',
            'summaryCount',
            'sort'
        ));
    }

    public function show(Vehicle $vehicle): View
    {
        $vehicle->load(['rentalCompany', 'images']);

        if ($vehicle->status !== Vehicle::STATUS_ACTIVE || !$vehicle->rentalCompany || $vehicle->rentalCompany->status_verification !== 'approved') {
            abort(404);
        }

        $baseReviewQuery = Review::query()
            ->where('vehicle_id', $vehicle->id)
            ->whereHas('booking', function ($query): void {
                $query->where('booking_status', Booking::BOOKING_COMPLETED);
            });

        $averageRating = (clone $baseReviewQuery)->avg('rating');
        $totalReviews = (clone $baseReviewQuery)->count();

        $ratingBreakdown = [];
        for ($star = 5; $star >= 1; $star--) {
            $ratingBreakdown[$star] = (clone $baseReviewQuery)->where('rating', $star)->count();
        }

        $reviews = (clone $baseReviewQuery)
            ->with('customer')
            ->latest('id')
            ->take(8)
            ->get();

        $averageRating = $averageRating ? round((float) $averageRating, 1) : 0;

        return view('detail-mobil.index', compact('vehicle', 'reviews', 'averageRating', 'totalReviews', 'ratingBreakdown'));
    }
}
