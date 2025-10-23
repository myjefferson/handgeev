<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->unsignedInteger('max_workspaces')->nullable()->default(1);
            $table->unsignedInteger('max_topics')->nullable()->default(3);
            $table->unsignedInteger('max_fields')->nullable()->default(10);
            $table->boolean('can_export')->default(false);
            $table->boolean('can_use_api')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('api_requests_per_minute')->default(60);
            $table->unsignedInteger('api_requests_per_hour')->default(1000);
            $table->unsignedInteger('api_requests_per_day')->default(10000);
            $table->unsignedInteger('burst_requests')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};