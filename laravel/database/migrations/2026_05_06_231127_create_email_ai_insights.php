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
        Schema::create('email_ai_insights', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('email_id')->constrained('emails')->cascadeOnDelete();
            
            $table->text('short_summary')->nullable();
            $table->longText('detailed_summary')->nullable();
            $table->longText('action_needed')->nullable();
            
            $table->enum('ai_language',['en', 'ro'])->default('en');
            
            $table->enum('urgency', ['low', 'medium', 'high'])->default('low');
            $table->enum('tone', ['positive', 'neutral', 'negative', 'formal', 'informal', 'urgent'])->nullable();
            $table->enum('intent', [
                'informative',
                'requesting',
                'thankful',
                'apologetic',
                'complaining',
                'advisory',
                'congratulatory',
                'commanding',

                'promotional',
                'notification',
                'reminder',
                'confirmation',
                'warning',
                'invitation',

                'other',
            ])->nullable();
            $table->json('secondary_intents')->nullable();
           
            $table->longText('raw_response')->nullable();
            $table->string('model_used')->default('unknown');
            $table->timestamps();

            $table->unique(['email_id', 'model_used']);
            $table->index('intent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_ai_insights');
    }
};
