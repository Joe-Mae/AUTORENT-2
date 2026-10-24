<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'type', 'vehicle_id', 'driver_id',
        'first_name', 'last_name', 'phone', 'email', 'birth_date',
        'id_card_number', 'address',
        'starts_at', 'ends_at', 'days_count', 'pickup_location', 'notes',
        'vehicle_daily_price', 'driver_daily_price', 'total_price', 'currency',
        'event_reason', 'itinerary', 'passenger_count',
        'client_vehicle_brand', 'client_vehicle_plate',
        'status', 'consent_accepted_at', 'internal_notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => ReservationType::class,
            'status' => ReservationStatus::class,
            'birth_date' => 'date',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'days_count' => 'integer',
            'vehicle_daily_price' => 'decimal:2',
            'driver_daily_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'passenger_count' => 'integer',
            'consent_accepted_at' => 'datetime',
        ];
    }

    /* --- Relations --- */

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ReservationDocument::class);
    }

    public function identityDocuments(): HasMany
    {
        return $this->documents()->whereIn('type', ['identity_front', 'identity_back']);
    }

    public function statusHistories(): HasMany
    {
        // Tri par id : deux transitions dans la même seconde gardent
        // leur ordre réel (le tri par date serait ambigu).
        return $this->hasMany(ReservationStatusHistory::class)->latest('id');
    }

    /* --- Présentation --- */

    public function clientFullName(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function hasIdentityDocument(): bool
    {
        return $this->identityDocuments()->exists();
    }

    /* --- Scopes --- */

    public function scopeStatus(Builder $query, ReservationStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeBlocking(Builder $query): Builder
    {
        $statuses = [ReservationStatus::Confirmed, ReservationStatus::Ongoing];

        if (setting('pending_blocks_vehicle', '0') === '1') {
            $statuses[] = ReservationStatus::Pending;
        }

        return $query->whereIn('status', $statuses);
    }

    /**
     * Réservations qui chevauchent une période donnée (bornes exclues).
     */
    public function scopeOverlapping(Builder $query, $startsAt, $endsAt): Builder
    {
        return $query->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt);
    }
}
