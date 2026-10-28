<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Encaissement réalisé à l'agence pour une réservation (§5.4 : le
 * règlement se fait à l'agence, pas en ligne).
 */
class Payment extends Model
{
    protected $fillable = [
        'reservation_id', 'received_by', 'amount', 'currency',
        'method', 'reference', 'note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'method' => PaymentMethod::class,
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
