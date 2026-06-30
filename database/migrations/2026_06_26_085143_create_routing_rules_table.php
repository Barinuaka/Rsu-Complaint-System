<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routing_rules', function (Blueprint $table) {
            $table->id();

            // The three-key lookup combination
            $table->unsignedBigInteger('category_id');            // FK → categories
            $table->unsignedBigInteger('campus_id');              // FK → campuses
            $table->enum('urgency_level', [
                'low', 'medium', 'high', 'critical'
            ]);

            // Who handles it
            $table->unsignedBigInteger('primary_handler_role_id');      // FK → roles
            $table->unsignedBigInteger('escalation_handler_role_id');   // FK → roles
            $table->unsignedBigInteger('secondary_escalation_role_id')  // FK → roles
                  ->nullable();                                          // Optional 3rd tier

            // How long before escalation triggers
            $table->unsignedSmallInteger('sla_hours');            // e.g. 24, 48, 72

            // Should central admin be copied on this type?
            $table->boolean('notify_central_admin')->default(false);

            $table->timestamps();

            // Prevent duplicate rules for same combination
            $table->unique(['category_id', 'campus_id', 'urgency_level'],
                           'unique_routing_rule');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routing_rules');
    }
};