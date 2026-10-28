<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 8)->default('FCFA');
            $table->enum('method', ['cash', 'mobile_money', 'card', 'transfer']);
            $table->string('reference')->nullable(); // n° transaction mobile money, reçu…
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['created_at']);
            $table->index(['reservation_id', 'method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
