<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();                                        // Auto CampusID
            $table->string('campus_name', 100)->unique();        // e.g. "Nkpolu-Oroworukwo"
            $table->string('campus_code', 10)->unique();         // e.g. "NKP", "EMU"
            $table->boolean('is_main_campus')->default(false);   // true only for Nkpolu
            $table->unsignedBigInteger('local_admin_id')         // Campus admin user
                  ->nullable();                                  // nullable = optional for now
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campuses');
    }
};