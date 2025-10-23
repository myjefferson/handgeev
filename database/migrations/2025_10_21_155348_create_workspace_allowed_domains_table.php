<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace_allowed_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->string('domain');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Índice único
            $table->unique(['workspace_id', 'domain']);
            
            // Índice para buscas por domínio
            $table->index('domain');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_allowed_domains');
    }
};