<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloraRide | Rental Kendaraan Premium</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body class="home-page">
    @include('home.navbar')

    <main>
        @include('home.hero')
        @include('home.form-pencarian-cepat')
        @include('home.keunggulan-layanan')
        @include('home.kendaraan-unggulan')
        @include('home.promo')
        @include('home.testimoni')
    </main>

    @include('home.footer')

    <script src="{{ asset('js/home.js') }}"></script>
</body>
</html>