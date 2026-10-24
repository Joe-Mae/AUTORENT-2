<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservation_status_histories', function (Blueprint $table) {
            // Notes d'état du véhicule : kilométrage, carburant, dégâts…
            $table->json('properties')->nullable()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('reservation_status_histories', function (Blueprint $table) {
            $table->dropColumn('properties');
        });
    }
};
