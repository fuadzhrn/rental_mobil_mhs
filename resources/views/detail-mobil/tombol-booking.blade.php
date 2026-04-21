<aside class="booking-sticky-card" aria-label="Tombol booking kendaraan">
    <p class="booking-label">Ringkasan Booking</p>
    <h3>Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }} <span>/ hari</span></h3>

    <ul class="booking-meta">
        <li><i class="fa-solid fa-circle-check"></i> Status: Tersedia</li>
        <li><i class="fa-solid fa-location-dot"></i> Pickup: {{ $vehicle->rentalCompany?->city }}</li>
        <li><i class="fa-solid fa-shield"></i> Rental terverifikasi</li>
    </ul>

    @php
        $bookingUrl = auth()->guest()
            ? route('login')
            : (auth()->user()->role === 'customer' ? route('booking.create', $vehicle) : route('home'));

        $bookingText = auth()->guest()
            ? 'Login untuk Booking'
            : (auth()->user()->role === 'customer' ? 'Booking Sekarang' : 'Kembali ke Home');
    @endphp

    <a href="{{ $bookingUrl }}" class="btn btn-primary full-width">{{ $bookingText }}</a>
    <a href="mailto:{{ $vehicle->rentalCompany?->email }}" class="btn btn-outline full-width">Hubungi Rental</a>
    <a href="{{ route('katalog.index') }}" class="text-link">Lihat Kendaraan Lain</a>
</aside>
