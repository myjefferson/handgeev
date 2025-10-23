<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace_api_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->string('endpoint');
            $table->json('allowed_methods');
            $table->timestamps();

            // Índice único
            $table->unique(['workspace_id', 'endpoint']);
            
            // Índices adicionais
            $table->index('workspace_id');
            $table->index('endpoint');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_api_permissions');
    }
};