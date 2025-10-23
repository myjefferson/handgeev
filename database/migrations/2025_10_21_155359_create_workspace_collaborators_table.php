<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace_collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('role', ['owner', 'admin', 'editor', 'viewer'])->default('viewer');
            $table->string('invitation_email')->nullable();
            $table->string('invitation_token', 64)->nullable();
            $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->text('request_message')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->text('response_reason')->nullable();
            $table->enum('request_type', ['invitation', 'edit_request'])->default('invitation');
            $table->timestamps();

            // Índices
            $table->unique('invitation_token');
            $table->index(['workspace_id', 'user_id']);
            $table->index('invitation_token');
            $table->index('status');
            $table->index('invitation_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_collaborators');
    }
};