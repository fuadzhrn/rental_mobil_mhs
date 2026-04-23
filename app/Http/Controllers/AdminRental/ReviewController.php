<?php

namespace App\Http\Controllers\AdminRental;

use App\Http\Controllers\Controller;
use App\Models\RentalCompany;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $rentalCompany = Auth::user()?->rentalCompany;

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company.');
        }

        $this->authorize('viewAny', Review::class);

        $reviews = Review::query()
            ->with(['vehicle', 'customer', 'booking'])
            ->where('rental_company_id', $rentalCompany->id)
            ->latest('id')
            ->paginate(10);

        return view('admin-rental.reviews.index', compact('reviews', 'rentalCompany'));
    }
}
