<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $summary = [
            ['label' => 'Total Rental', 'value' => '128'],
            ['label' => 'Total User', 'value' => '2.540'],
            ['label' => 'Total Komisi', 'value' => 'Rp 48.500.000'],
        ];

        return view('super-admin.dashboard', compact('summary'));
    }
}
