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
        Schema::create('workspace_threats', function (Blueprint $table) {
            $table->Uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->foreignUuid('email_account_id')->constrained('email_accounts')->cascadeOnDelete();
            $table->foreignUuid('email_id')->constrained('emails')->cacadeOnDelete();
            $table->foreignUuid('email_security_scan_id')->constrained('email_security_scans')->cascadeOnDelete();

            $table->enum('severity',['minimal', 'low', 'medium', 'high', 'critical']);
            $table->enum('status', ['new', 'reviewed', 'dismissed', 'resolved']);
            $table->timestampTz('detected_at');
            $table->timestampTz('resolved_at')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threats');
    }
};
