<?php

namespace App\Http\Controllers\AdminRental;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $summary = [
            ['label' => 'Total Kendaraan', 'value' => '68'],
            ['label' => 'Booking Aktif', 'value' => '24'],
            ['label' => 'Customer', 'value' => '1.120'],
        ];

        return view('admin-rental.dashboard', compact('summary'));
    }
}
