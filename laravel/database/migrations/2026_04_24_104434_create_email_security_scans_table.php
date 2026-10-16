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
        Schema::create('email_security_scans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('email_id')->constrained('emails')->cascadeOnDelete();
            
            $table->float('score')->default(0);
            $table->enum('risk',['unscanned', 'minimal', 'low', 'medium', 'high', 'critical'])->default('unscanned');
            $table->json('security_reasons')->nullable();
           
            $table->enum('status', ['pending', 'completed', 'failed', 'scanning'])->default('pending');
            $table->longText('fail_reason')->nullable();
            
            $table->timestampTz('scanned_at')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_security_scans');
    }
};
