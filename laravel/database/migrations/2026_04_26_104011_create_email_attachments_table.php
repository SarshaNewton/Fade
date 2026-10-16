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
        Schema::create('email_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('email_id')->constrained('emails')->cascadeOnDelete();

            $table->string('file_name');
            $table->string('stored_name');
            $table->string('mime');
            $table->bigInteger('size');

            $table->string('path');
            $table->string('disk')->default('local');

            $table->string('content_id')->nullable();
            $table->boolean('is_inline')->default(false);

            $table->string('hash')->nullable();

            $table->boolean('is_suspicious')->default(false);
            $table->json('detected_risks')->nullable();
            $table->timestamp('scanned_at')->nullable();
            
            $table->timestamps();

            $table->index('email_id');
            $table->index('hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_attachments');
    }
};
