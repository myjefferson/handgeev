<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('stripe_event_id');
            $table->string('event_type', 100);
            $table->json('payload');
            $table->timestamp('processed_at')->useCurrent();
            
            // Chave única
            $table->unique('stripe_event_id');
            
            // Índices
            $table->index('event_type');
            $table->index('processed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};