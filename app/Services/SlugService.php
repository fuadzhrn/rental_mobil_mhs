<?php

namespace App\Services;

use Illuminate\Support\Str;

class SlugService
{
    public function generateUnique(string $modelClass, string $column, string $source, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($source);
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'item';

        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($modelClass, $column, $slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $modelClass, string $column, string $slug, ?int $ignoreId): bool
    {
        return $modelClass::query()
            ->when($ignoreId, function ($query) use ($ignoreId): void {
                $query->where('id', '!=', $ignoreId);
            })
            ->where($column, $slug)
            ->exists();
    }
}
