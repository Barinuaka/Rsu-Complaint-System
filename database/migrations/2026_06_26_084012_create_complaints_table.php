<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();

            // --- Tracking & Identity ---
            $table->uuid('tracking_token')->unique();         // Public-facing UUID reference
            $table->unsignedBigInteger('submitter_id')        // Who submitted it
                  ->nullable();                              // null = anonymous submission
            $table->boolean('is_anonymous')->default(false);  // Anonymous flag

            // --- Complaint Content ---
            $table->string('complaint_title', 200);           // Short title
            $table->text('complaint_text');                   // Full narrative
            $table->string('evidence_file_path', 500)         // Uploaded file path
                  ->nullable();

            // --- NLP Triage Results ---
            $table->unsignedBigInteger('category_id')         // FK → categories
                  ->nullable();
            $table->enum('urgency_level', [                   // VADER urgency output
                'low', 'medium', 'high', 'critical'
            ])->default('medium');
            $table->decimal('nlp_confidence', 5, 4)           // e.g. 0.8734
                  ->nullable();
            $table->decimal('vader_compound_score', 5, 4)     // Range: -1.0 to 1.0
                  ->nullable();

            // --- Routing & Assignment ---
            $table->unsignedBigInteger('campus_id')           // Which campus submitted
                  ->nullable();
            $table->unsignedBigInteger('assigned_to')         // Handling officer (FK → users)
                  ->nullable();

            // --- Lifecycle Status ---
            $table->enum('current_status', [
                'submitted',      // Just received
                'triaged',        // NLP processed
                'assigned',       // Sent to handler
                'in_review',      // Handler is working on it
                'escalated',      // SLA breached, moved up
                'resolved',       // Formally closed
                'closed'          // Archived
            ])->default('submitted');

            // --- SLA & Escalation ---
            $table->timestamp('sla_deadline_at')->nullable();  // Computed deadline
            $table->unsignedTinyInteger('escalation_count')    // How many times escalated
                  ->default(0);

            // --- Resolution ---
            $table->timestamp('resolved_at')->nullable();      // When it was closed
            $table->text('resolution_note')->nullable();       // Officer's closing note
            $table->unsignedTinyInteger('satisfaction_rating') // Student feedback 1-5
                  ->nullable();

            $table->timestamps();                              // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};