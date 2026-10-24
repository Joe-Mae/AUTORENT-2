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

    /**
     * URL relative à la requête courante : fonctionne quel que soit le
     * domaine de l'entreprise (marque blanche), contrairement à l'URL
     * absolue du disque qui dépend de APP_URL.
     */
    public function url(): string
    {
        return asset('storage/'.$this->path);
    }

    protected static function booted(): void
    {
        // Supprime le fichier du disque public avec la ligne.
        static::deleted(function (self $image): void {
            Storage::disk('public')->delete($image->path);
        });
    }
}
