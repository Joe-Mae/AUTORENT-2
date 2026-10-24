<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class VehicleImage extends Model
{
    protected $fillable = ['vehicle_id', 'path', 'is_primary', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    protected static function booted(): void
    {
        // Supprime le fichier du disque public avec la ligne.
        static::deleted(function (self $image): void {
            Storage::disk('public')->delete($image->path);
        });
    }
}
