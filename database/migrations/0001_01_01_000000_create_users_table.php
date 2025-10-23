<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30);
            $table->string('surname', 30)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->string('email', 40);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('timezone', 50)->default('UTC');
            $table->string('language', 10)->default('en');
            $table->text('password');
            $table->string('phone', 20)->nullable();
            $table->text('global_key_api')->nullable();
            $table->string('email_verification_code', 255)->nullable();
            $table->timestamp('email_verification_sent_at')->nullable();
            $table->boolean('email_verified')->default(false);
            
            // Campos Stripe
            $table->string('stripe_id', 255)->nullable();
            $table->string('pm_type', 255)->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->string('stripe_customer_id', 255)->nullable();
            $table->string('stripe_subscription_id', 255)->nullable();
            $table->timestamp('plan_expires_at')->nullable();
            
            // Status usando enum nativo do PostgreSQL
            $table->enum('status', ['active', 'inactive', 'suspended', 'past_due', 'unpaid', 'incomplete', 'trial'])->default('active');
            
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            
            $table->timestamp('deleted_at')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('email');
            $table->index('status');
            $table->index('stripe_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};