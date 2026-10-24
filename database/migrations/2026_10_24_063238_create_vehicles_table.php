<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_category_id')->constrained()->restrictOnDelete();
            $table->string('brand');
            $table->string('model');
            $table->unsignedSmallInteger('year');
            $table->string('registration_plate')->unique();
            $table->unsignedTinyInteger('seats');
            $table->enum('transmission', ['automatic', 'manual']);
            $table->enum('fuel', ['petrol', 'diesel', 'hybrid', 'electric']);
            $table->unsignedInteger('mileage_km')->default(0);
            $table->decimal('daily_price', 10, 2);
            $table->enum('status', ['available', 'maintenance', 'unavailable'])->default('available');
            $table->json('features')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'daily_price']);
            $table->index(['brand', 'model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
