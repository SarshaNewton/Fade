<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workspace_invitations', function (Blueprint $table) {
            $table->uuid('id');

            $table->foreignUuid('workspace_id')->cascadeOnDelete();
            $table->foreignUuid('invited_by')->constrained('users')->CascadeOnDelete();

            $table->string('invited_email');
            $table->timestampTz('invited_at');
            $table->timestampTz('expires_at');
            $table->timestampTz('accepted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_invitations');
    }
};
