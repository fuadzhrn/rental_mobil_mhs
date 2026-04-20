<?php

namespace App\Http\Controllers\AdminRental;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\RentalCompany;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company. Silakan lengkapi data rental company terlebih dahulu.');
        }

        $vehicles = Vehicle::query()
            ->where('rental_company_id', $rentalCompany->id)
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();

                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('brand', 'like', '%' . $search . '%')
                        ->orWhere('category', 'like', '%' . $search . '%');
                });
            })
            ->withCount('images')
            ->with(['images'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin-rental.vehicles.index', compact('vehicles', 'rentalCompany'));
    }

    public function create(): View|RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company. Silakan lengkapi data rental company terlebih dahulu.');
        }

        $vehicle = new Vehicle([
            'status' => Vehicle::STATUS_ACTIVE,
        ]);

        return view('admin-rental.vehicles.create', compact('vehicle', 'rentalCompany'));
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company. Silakan lengkapi data rental company terlebih dahulu.');
        }

        $validated = $request->validated();
        $slug = $this->generateUniqueSlug($validated['name']);

        $mainImagePath = null;
        if ($request->hasFile('main_image')) {
            $mainImagePath = $request->file('main_image')->store('vehicles/main', 'public');
        }

        $vehicle = Vehicle::create([
            'rental_company_id' => $rentalCompany->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'brand' => $validated['brand'],
            'type' => $validated['type'],
            'category' => $validated['category'],
            'year' => $validated['year'],
            'transmission' => $validated['transmission'],
            'fuel_type' => $validated['fuel_type'],
            'seat_capacity' => $validated['seat_capacity'],
            'luggage_capacity' => $validated['luggage_capacity'] ?? null,
            'color' => $validated['color'] ?? null,
            'price_per_day' => $validated['price_per_day'],
            'description' => $validated['description'] ?? null,
            'terms_conditions' => $validated['terms_conditions'] ?? null,
            'status' => $validated['status'],
            'main_image' => $mainImagePath,
        ]);

        $this->storeGalleryImages($request, $vehicle);

        return redirect()
            ->route('admin-rental.vehicles.index')
            ->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle): View|RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company. Silakan lengkapi data rental company terlebih dahulu.');
        }

        $this->ensureVehicleBelongsToRental($vehicle, $rentalCompany->id);

        $vehicle->load('images');

        return view('admin-rental.vehicles.edit', compact('vehicle', 'rentalCompany'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company. Silakan lengkapi data rental company terlebih dahulu.');
        }

        $this->ensureVehicleBelongsToRental($vehicle, $rentalCompany->id);

        $validated = $request->validated();

        if ($request->hasFile('main_image')) {
            $this->deleteStoredFile($vehicle->main_image);
            $vehicle->main_image = $request->file('main_image')->store('vehicles/main', 'public');
        }

        $vehicle->fill([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['name'], $vehicle->id),
            'brand' => $validated['brand'],
            'type' => $validated['type'],
            'category' => $validated['category'],
            'year' => $validated['year'],
            'transmission' => $validated['transmission'],
            'fuel_type' => $validated['fuel_type'],
            'seat_capacity' => $validated['seat_capacity'],
            'luggage_capacity' => $validated['luggage_capacity'] ?? null,
            'color' => $validated['color'] ?? null,
            'price_per_day' => $validated['price_per_day'],
            'description' => $validated['description'] ?? null,
            'terms_conditions' => $validated['terms_conditions'] ?? null,
            'status' => $validated['status'],
        ]);

        $vehicle->save();

        $this->storeGalleryImages($request, $vehicle);
        $this->deleteSelectedGalleryImages($request, $vehicle);

        return redirect()
            ->route('admin-rental.vehicles.index')
            ->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company. Silakan lengkapi data rental company terlebih dahulu.');
        }

        $this->ensureVehicleBelongsToRental($vehicle, $rentalCompany->id);

        $this->deleteStoredFile($vehicle->main_image);

        foreach ($vehicle->images as $image) {
            $this->deleteStoredFile($image->image_path);
            $image->delete();
        }

        $vehicle->delete();

        return redirect()
            ->route('admin-rental.vehicles.index')
            ->with('success', 'Data kendaraan berhasil dihapus.');
    }

    public function destroyGalleryImage(VehicleImage $image): RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company. Silakan lengkapi data rental company terlebih dahulu.');
        }

        $image->load('vehicle');
        $this->ensureVehicleBelongsToRental($image->vehicle, $rentalCompany->id);

        $this->deleteStoredFile($image->image_path);
        $image->delete();

        return back()->with('success', 'Gambar galeri berhasil dihapus.');
    }

    private function getRentalCompany(): ?RentalCompany
    {
        return Auth::user()?->rentalCompany;
    }

    private function ensureVehicleBelongsToRental(Vehicle $vehicle, int $rentalCompanyId): void
    {
        if ((int) $vehicle->rental_company_id !== $rentalCompanyId) {
            abort(404);
        }
    }

    private function generateUniqueSlug(string $name, ?int $ignoreVehicleId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Vehicle::query()
                ->when($ignoreVehicleId, fn ($query) => $query->where('id', '!=', $ignoreVehicleId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function storeGalleryImages(StoreVehicleRequest|UpdateVehicleRequest $request, Vehicle $vehicle): void
    {
        if (!$request->hasFile('gallery_images')) {
            return;
        }

        foreach ($request->file('gallery_images') as $index => $imageFile) {
            $imagePath = $imageFile->store('vehicles/gallery', 'public');

            VehicleImage::create([
                'vehicle_id' => $vehicle->id,
                'image_path' => $imagePath,
                'is_primary' => false,
            ]);
        }
    }

    private function deleteSelectedGalleryImages(UpdateVehicleRequest $request, Vehicle $vehicle): void
    {
        $deleteIds = $request->input('delete_gallery_images', []);

        if (empty($deleteIds)) {
            return;
        }

        $galleryImages = VehicleImage::query()
            ->where('vehicle_id', $vehicle->id)
            ->whereIn('id', $deleteIds)
            ->get();

        foreach ($galleryImages as $image) {
            $this->deleteStoredFile($image->image_path);
            $image->delete();
        }
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
