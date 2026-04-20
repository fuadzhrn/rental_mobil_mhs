@php
    $mainImageUrl = $vehicle->main_image
        ? asset('storage/' . $vehicle->main_image)
        : ($vehicle->images->first() ? asset('storage/' . $vehicle->images->first()->image_path) : null);

    $thumbnailImages = $vehicle->images;
@endphp

<section class="detail-gallery-card" aria-label="Galeri foto kendaraan">
    <figure class="gallery-main-photo">
        @if ($mainImageUrl)
            <img id="mainVehicleImage" src="{{ $mainImageUrl }}" alt="{{ $vehicle->name }}">
        @else
            <div id="mainVehicleImage" style="min-height:380px; display:flex; align-items:center; justify-content:center; background:#e5e7eb; color:#6b7280; border-radius:18px;">No Image</div>
        @endif
    </figure>

    <div class="gallery-thumbnails">
        @if ($vehicle->main_image)
            <button class="thumb is-active" type="button" data-image="{{ asset('storage/' . $vehicle->main_image) }}">
                <img src="{{ asset('storage/' . $vehicle->main_image) }}" alt="Thumbnail {{ $vehicle->name }}">
            </button>
        @endif

        @forelse ($thumbnailImages as $image)
            <button class="thumb" type="button" data-image="{{ asset('storage/' . $image->image_path) }}">
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thumbnail {{ $vehicle->name }}">
            </button>
        @empty
            @if (!$vehicle->main_image)
                <div style="padding:12px; color:#6b7280;">Belum ada galeri foto.</div>
            @endif
        @endforelse
    </div>
</section>
