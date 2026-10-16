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
        DB::statement('ALTER TABLE mailboxes DROP CONSTRAINT mailboxes_role_check');

        DB::statement("
            ALTER TABLE mailboxes
            ADD CONSTRAINT mailboxes_role_check
            CHECK (role IN ('inbox', 'draft', 'sent', 'trash', 'spam', 'archive', 'outbox', 'other'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
