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
        Schema::create('emails', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('mailbox_id')->constrained('mailboxes')->cascadeOnDelete();
            $table->foreignUuid('email_account_id')->constrained('email_accounts')->cascadeOnDelete();

            $table->enum('direction', ['inbound', 'outbound']);
            //Only used for sent emails
            $table->enum('delivery_status', ['draft','pending', 'sent', 'failed', 'bounced'])->nullable();
            $table->text('subject')->nullable();
            $table->text('body_snippet')->nullable();
            $table->longText('body_text')->nullable();
            $table->longText('body_html')->nullable();

            $table->string('from_email_address');
            $table->string('from_name')->nullable();

            $table->boolean('is_unread')->default(true);
            $table->boolean('is_starred')->default(false);
            
            $table->timestampTz('date_header')->nullable();
            $table->timestampTz('internal_date')->nullable();
            $table->timestampTz('received_at')->nullable();
            $table->timestampTz('sent_at')->nullable();
            $table->timestampTz('read_at')->nullable();
            
            $table->unsignedBigInteger('imap_uid')->nullable();

            $table->string('provider_id')->nullable();
            $table->string('remote_thread_id')->nullable();
            $table->string('message_id_header')->nullable();

            $table->json('headers')->nullable();
            $table->longText('raw_headers')->nullable();

            $table->timestampsTz();

            $table->unique(['mailbox_id', 'imap_uid']);
            $table->index(['email_account_id', 'internal_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
