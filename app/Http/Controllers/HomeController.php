<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Promo;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil kendaraan unggulan (featured vehicles) - limit 4
        $featuredVehicles = Vehicle::where('status', 'active')
            ->with(['rentalCompany', 'images'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->take(4)
            ->get();

        // Ambil promo aktif (active promotions)
        $promos = Promo::where('status', 'active')
            ->where('end_date', '>=', now())
            ->with('rentalCompany')
            ->get();

        // Ambil testimoni (reviews) - limit 3 dengan rating tertinggi
        $testimonials = Review::with(['customer', 'vehicle'])
            ->orderByDesc('rating')
            ->take(3)
            ->get();

        // Ambil kategori kendaraan untuk filter
        $vehicleCategories = Vehicle::where('status', 'active')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return view('home.index', compact(
            'featuredVehicles',
            'promos',
            'testimonials',
            'vehicleCategories'
        ));
    }

    public function search()
    {
        $lokasi = request('lokasi');
        $tanggal_sewa = request('tanggal_sewa');
        $durasi = request('durasi');
        $jenis_kendaraan = request('jenis_kendaraan');

        // Redirect ke katalog dengan query parameters
        return redirect()->route('katalog.index', [
            'lokasi' => $lokasi,
            'tanggal_sewa' => $tanggal_sewa,
            'durasi' => $durasi,
            'category' => $jenis_kendaraan,
        ]);
    }
}
