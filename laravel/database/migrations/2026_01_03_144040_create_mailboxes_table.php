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
        Schema::create('mailboxes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('email_account_id')->constrained('email_accounts')->cascadeOnDelete();
            
            $table->string('name');
            $table->string('imap_path')->nullable();

            $table->enum('role', ['inbox', 'draft', 'sent', 'trash', 'spam', 'archive', 'other']);
            $table->boolean('is_system');

            $table->unsignedBigInteger('uid_validity')->nullable();
            $table->unsignedBigInteger('last_seen_uid')->nullable();

            $table->timestampsTz();

            $table->unique(['email_account_id', 'imap_path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailboxes');
    }
};
