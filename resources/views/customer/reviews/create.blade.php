<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloraRide | Beri Ulasan {{ $booking->booking_code }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/home-mobile.css') }}">
</head>
<body style="margin:0; font-family:'Poppins',sans-serif; background:#f4f6fa; color:#111827;">
    @include('home.navbar')

    @php
        $imagePath = $booking->vehicle->main_image
            ? asset('storage/' . $booking->vehicle->main_image)
            : ($booking->vehicle->primaryImage?->image_path ? asset('storage/' . $booking->vehicle->primaryImage->image_path) : null);
    @endphp

    <main style="max-width:920px; margin:32px auto; padding:0 16px; display:grid; gap:16px;">
        <section style="background:#fff; border-radius:18px; padding:20px; box-shadow:0 10px 24px rgba(15,23,42,.06);">
            <a href="{{ route('customer.bookings.show', $booking) }}" style="text-decoration:none; color:#334155; font-weight:600;">&larr; Kembali ke Detail Booking</a>
            <h1 style="margin:10px 0 0; font-family:'Montserrat',sans-serif;">Beri Ulasan</h1>
            <p style="margin:8px 0 0; color:#6b7280;">Ulasan Anda membantu customer lain mengambil keputusan dengan lebih baik.</p>
        </section>

        <section style="background:#fff; border-radius:18px; padding:20px; display:grid; gap:16px;">
            <div style="display:flex; gap:12px; align-items:center;">
                @if ($imagePath)
                    <img src="{{ $imagePath }}" alt="{{ $booking->vehicle->name }}" style="width:84px; height:84px; object-fit:cover; border-radius:12px;">
                @endif
                <div>
                    <div style="font-weight:700; font-size:17px;">{{ $booking->vehicle->name }}</div>
                    <div style="color:#6b7280;">{{ $booking->vehicle->rentalCompany?->company_name }}</div>
                    <small style="color:#6b7280;">{{ $booking->booking_code }} | {{ $booking->pickup_date->format('d M Y') }} - {{ $booking->return_date->format('d M Y') }}</small>
                </div>
            </div>

            <form method="POST" action="{{ route('customer.reviews.store', $booking) }}" style="display:grid; gap:14px;">
                @csrf

                <div>
                    <label for="rating" style="display:block; font-weight:600; margin-bottom:6px;">Rating (1-5)</label>
                    <select id="rating" name="rating" required style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
                        <option value="">Pilih rating</option>
                        @for ($star = 5; $star >= 1; $star--)
                            <option value="{{ $star }}" @selected(old('rating') == $star)>{{ $star }} Bintang</option>
                        @endfor
                    </select>
                    @error('rating')
                        <small style="color:#dc2626; display:block; margin-top:5px;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="review" style="display:block; font-weight:600; margin-bottom:6px;">Ulasan (opsional)</label>
                    <textarea id="review" name="review" rows="5" placeholder="Ceritakan pengalaman sewa Anda..." style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px; resize:vertical;">{{ old('review') }}</textarea>
                    @error('review')
                        <small style="color:#dc2626; display:block; margin-top:5px;">{{ $message }}</small>
                    @enderror
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <a href="{{ route('customer.bookings.show', $booking) }}" style="text-decoration:none; border:1px solid #cbd5e1; color:#334155; padding:10px 14px; border-radius:10px; font-weight:600;">Batal</a>
                    <button type="submit" style="border:0; background:#0f172a; color:#fff; padding:10px 16px; border-radius:10px; font-weight:700; cursor:pointer;">Kirim Ulasan</button>
                </div>
            </form>
        </section>
    </main>

    @include('home.footer')
    <script src="{{ asset('assets/js/home-mobile.js') }}"></script>
</body>
</html>
