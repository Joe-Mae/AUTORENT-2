<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // ex. PDJ-2026-000123
            $table->enum('type', ['vehicle', 'vehicle_with_driver', 'driver_only']);
            $table->foreignId('vehicle_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->restrictOnDelete();

            // Client (aucun compte : tout est stocké ici)
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('email');
            $table->date('birth_date')->nullable();
            $table->string('id_card_number')->nullable();
            $table->string('address')->nullable();

            // Location
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->unsignedSmallInteger('days_count');
            $table->string('pickup_location')->nullable();
            $table->text('notes')->nullable(); // remarques du client

            // Tarifs figés au moment de la réservation
            $table->decimal('vehicle_daily_price', 10, 2)->default(0);
            $table->decimal('driver_daily_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->string('currency', 8)->default('FCFA');

            // Service chauffeur (voiture+chauffeur et chauffeur seul)
            $table->string('event_reason')->nullable(); // mariage, déplacement pro…
            $table->text('itinerary')->nullable();
            $table->unsignedTinyInteger('passenger_count')->nullable();
            $table->string('client_vehicle_brand')->nullable(); // chauffeur seul
            $table->string('client_vehicle_plate')->nullable(); // chauffeur seul

            $table->enum('status', ['pending', 'identity_verified', 'confirmed', 'ongoing', 'completed', 'cancelled', 'rejected'])->default('pending');
            $table->datetime('consent_accepted_at')->nullable(); // consentement pièce d'identité
            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'starts_at', 'ends_at']);
            $table->index(['vehicle_id', 'starts_at', 'ends_at']);
            $table->index(['driver_id', 'starts_at', 'ends_at']);
            $table->index(['email', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
