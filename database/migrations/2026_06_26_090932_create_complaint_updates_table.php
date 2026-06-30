<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_updates', function (Blueprint $table) {
            $table->id();

            // Which complaint this update belongs to
            $table->unsignedBigInteger('complaint_id');

            // Who performed the action (null = system-generated, e.g. auto-escalation)
            $table->unsignedBigInteger('updated_by')->nullable();

            // What type of action was taken
            $table->enum('action_type', [
                'submitted',          // Initial submission
                'nlp_triaged',        // NLP processed it
                'assigned',           // Routed to handler
                'status_changed',     // Handler updated status
                'manually_reclassified', // Officer overrode NLP category
                'escalated',          // SLA breached, moved up
                'resolved',           // Formally closed
                'reopened',           // Complainant challenged resolution
                'note_added'          // Officer added a note
            ]);

            // The status transition record
            $table->string('previous_status', 50)->nullable();  // What it was before
            $table->string('new_status', 50)->nullable();        // What it became

            // Human-readable note about this action
            $table->text('update_note')->nullable();

            // Immutable timestamp — no updated_at
            $table->timestamp('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_updates');
    }
};