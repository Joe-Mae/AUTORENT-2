<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Journal d'audit : chaque action sensible du back-office y est tracée
 * (changement de statut, consultation d'un document d'identité…).
 */
class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'description', 'properties'];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
