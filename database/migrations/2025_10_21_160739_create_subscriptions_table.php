<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('plan_id');
            $table->string('stripe_subscription_id');
            $table->string('stripe_price_id');
            $table->enum('status', ['active', 'canceled', 'past_due', 'unpaid', 'incomplete']);
            $table->timestamp('current_period_start');
            $table->timestamp('current_period_end');
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('previous_subscription_id')->nullable()->after('plan_id');
            $table->enum('upgrade_type', ['new', 'upgrade', 'downgrade', 'crossgrade'])->default('new')->after('status');
            $table->decimal('proration_amount', 10, 2)->nullable()->after('stripe_subscription_id');
            $table->timestamp('billing_cycle_anchor')->nullable()->after('ends_at');
            // Adicionar foreign key para histórico
            $table->foreign('previous_subscription_id')
                  ->references('id')
                  ->on('subscriptions')
                  ->onDelete('set null');
            
            // Chave única
            $table->unique('stripe_subscription_id');
            
            // Índices
            $table->index(['user_id', 'status']);
            $table->index('current_period_end');
            $table->index('previous_subscription_id', 'idx_previous_subscription');
            $table->index('upgrade_type', 'idx_upgrade_type');
            
            // Nota: A foreign key para plan_id foi comentada no SQL original
            // Se quiser habilitar, descomente a linha abaixo:
            $table->foreign('plan_id')->references('id')->on('plans');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};