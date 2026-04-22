<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    public function storePublic(UploadedFile $file, string $directory): string
    {
        $safeDirectory = trim($directory, '/');
        $filename = Str::uuid()->toString() . '.' . strtolower($file->getClientOriginalExtension());

        return $file->storeAs($safeDirectory . '/' . now()->format('Y/m'), $filename, 'public');
    }

    public function deletePublic(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
