<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('type_workspace_id')->constrained();
            $table->foreignId('type_view_workspace_id')->default(1)->constrained('type_views_workspaces');
            $table->string('title', 100);
            $table->string('description', 250)->nullable();
            $table->boolean('is_published')->default(false);
            $table->text('password')->nullable();
            $table->text('workspace_key_api')->nullable();
            $table->boolean('api_enabled')->default(false);
            $table->boolean('api_domain_restriction')->default(false);
            $table->boolean('api_jwt_required')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};