<?php

namespace App\Http\Controllers\AdminRental;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\RentalCompany;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Services\ActivityLogService;
use App\Services\FileUploadService;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function __construct(
        private readonly SlugService $slugService,
        private readonly FileUploadService $fileUploadService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

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
        $this->authorize('create', Vehicle::class);

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
        $this->authorize('create', Vehicle::class);

        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company. Silakan lengkapi data rental company terlebih dahulu.');
        }

        $validated = $request->validated();
        $slug = $this->slugService->generateUnique(Vehicle::class, 'slug', $validated['name']);

        $mainImagePath = null;
        try {
            if ($request->hasFile('main_image')) {
                $mainImagePath = $this->fileUploadService->storePublic($request->file('main_image'), 'vehicles/main');
            }
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'Upload foto utama gagal. Silakan coba lagi dengan file yang valid.');
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

        $this->activityLogService->log(
            action: 'vehicle.created',
            description: 'Admin rental membuat kendaraan baru: ' . $vehicle->name,
            targetType: 'vehicle',
            targetId: $vehicle->id,
            meta: ['slug' => $vehicle->slug]
        );

        return redirect()
            ->route('admin-rental.vehicles.index')
            ->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle): View|RedirectResponse
    {
        $this->authorize('update', $vehicle);

        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company. Silakan lengkapi data rental company terlebih dahulu.');
        }

        $vehicle->load('images');

        return view('admin-rental.vehicles.edit', compact('vehicle', 'rentalCompany'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $this->authorize('update', $vehicle);

        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company. Silakan lengkapi data rental company terlebih dahulu.');
        }

        $validated = $request->validated();

        try {
            if ($request->hasFile('main_image')) {
                $this->fileUploadService->deletePublic($vehicle->main_image);
                $vehicle->main_image = $this->fileUploadService->storePublic($request->file('main_image'), 'vehicles/main');
            }
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'Upload foto utama gagal. Silakan coba lagi dengan file yang valid.');
        }

        $vehicle->fill([
            'name' => $validated['name'],
            'slug' => $this->slugService->generateUnique(Vehicle::class, 'slug', $validated['name'], $vehicle->id),
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

        $this->activityLogService->log(
            action: 'vehicle.updated',
            description: 'Admin rental memperbarui kendaraan: ' . $vehicle->name,
            targetType: 'vehicle',
            targetId: $vehicle->id,
            meta: ['slug' => $vehicle->slug]
        );

        return redirect()
            ->route('admin-rental.vehicles.index')
            ->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $this->authorize('delete', $vehicle);

        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company. Silakan lengkapi data rental company terlebih dahulu.');
        }

        $this->fileUploadService->deletePublic($vehicle->main_image);

        foreach ($vehicle->images as $image) {
            $this->fileUploadService->deletePublic($image->image_path);
            $image->delete();
        }

        $deletedVehicleId = $vehicle->id;
        $deletedVehicleName = $vehicle->name;
        $vehicle->delete();

        $this->activityLogService->log(
            action: 'vehicle.deleted',
            description: 'Admin rental menghapus kendaraan: ' . $deletedVehicleName,
            targetType: 'vehicle',
            targetId: $deletedVehicleId
        );

        return redirect()
            ->route('admin-rental.vehicles.index')
            ->with('success', 'Data kendaraan berhasil dihapus.');
    }

    public function destroyGalleryImage(VehicleImage $image): RedirectResponse
    {
        $image->load('vehicle');
        $this->authorize('update', $image->vehicle);

        $this->fileUploadService->deletePublic($image->image_path);
        $image->delete();

        return back()->with('success', 'Gambar galeri berhasil dihapus.');
    }

    private function getRentalCompany(): ?RentalCompany
    {
        return Auth::user()?->rentalCompany;
    }

    private function storeGalleryImages(StoreVehicleRequest|UpdateVehicleRequest $request, Vehicle $vehicle): void
    {
        if (!$request->hasFile('gallery_images')) {
            return;
        }

        foreach ($request->file('gallery_images') as $imageFile) {
            $imagePath = $this->fileUploadService->storePublic($imageFile, 'vehicles/gallery');

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
            $this->fileUploadService->deletePublic($image->image_path);
            $image->delete();
        }
    }
}
