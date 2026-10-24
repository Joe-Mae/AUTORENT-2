<?php

namespace App\Models;

use App\Enums\FuelType;
use App\Enums\TransmissionType;
use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_category_id', 'brand', 'model', 'year', 'registration_plate',
        'seats', 'transmission', 'fuel', 'mileage_km', 'daily_price',
        'status', 'features', 'description',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'seats' => 'integer',
            'mileage_km' => 'integer',
            'daily_price' => 'decimal:2',
            'features' => 'array',
            'transmission' => TransmissionType::class,
            'fuel' => FuelType::class,
            'status' => VehicleStatus::class,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class, 'vehicle_category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(VehicleImage::class)->where('is_primary', true);
    }

    public function blockedPeriods(): HasMany
    {
        return $this->hasMany(VehicleBlockedPeriod::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function fullName(): string
    {
        return $this->brand.' '.$this->model;
    }

    /* --- Scopes --- */

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', VehicleStatus::Available);
    }

    public function scopeVisibleOnSite(Builder $query): Builder
    {
        return $query->available()->orderByDesc('daily_price');
    }
}
