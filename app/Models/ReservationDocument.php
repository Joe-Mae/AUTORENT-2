<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ReservationDocument extends Model
{
    protected $fillable = ['reservation_id', 'type', 'path', 'original_name', 'uploaded_by'];

    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Les documents d'identité vivent sur le disque privé : jamais d'URL
     * publique, l'accès passe par une route authentifiée à URL signée.
     */
    public function fullPath(): string
    {
        return Storage::disk('private')->path($this->path);
    }

    protected static function booted(): void
    {
        static::deleted(function (self $document): void {
            Storage::disk('private')->delete($document->path);
        });
    }
}
