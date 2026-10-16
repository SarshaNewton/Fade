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
        Schema::create('email_recipients', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('email_id')->constrained('emails')->cascadeOnDelete();

            $table->enum('type', ['to', 'cc', 'bcc']);
            $table->string('email_address');
            $table->string('name')->nullable();

            $table->timestampsTz();

            $table->unique(['email_id','type','email_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_receipents');
    }
};
