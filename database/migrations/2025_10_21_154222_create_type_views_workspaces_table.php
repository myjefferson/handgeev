<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('type_views_workspaces', function (Blueprint $table) {
            $table->id();
            $table->string('description', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('type_views_workspaces');
    }
};