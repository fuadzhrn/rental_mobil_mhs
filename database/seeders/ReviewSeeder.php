<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            ['booking_code' => 'BK-20260423-0001', 'rating' => 5, 'review' => 'Sangat puas, mobil bersih dan proses cepat.'],
            ['booking_code' => 'BK-20260423-0002', 'rating' => 4, 'review' => 'Layanan bagus dan admin responsif.'],
            ['booking_code' => 'BK-20260423-0003', 'rating' => 5, 'review' => 'Unit premium dalam kondisi prima.'],
        ];

        foreach ($reviews as $reviewData) {
            $booking = Booking::where('booking_code', $reviewData['booking_code'])->first();

            if (!$booking) {
                continue;
            }

            Review::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'customer_id' => $booking->customer_id,
                    'vehicle_id' => $booking->vehicle_id,
                    'rental_company_id' => $booking->rental_company_id,
                    'rating' => $reviewData['rating'],
                    'review' => $reviewData['review'],
                ]
            );
        }
    }
}