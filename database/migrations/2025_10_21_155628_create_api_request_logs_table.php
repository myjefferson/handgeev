<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->string('method', 10);
            $table->string('endpoint');
            $table->integer('response_code');
            $table->integer('response_time'); // em milissegundos
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Índices
            $table->index(['user_id', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index('created_at');
            $table->index('response_code');
            $table->index('method');
            $table->index('user_id');
            $table->index('workspace_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_request_logs');
    }
};