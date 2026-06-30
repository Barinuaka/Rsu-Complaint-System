<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();                          // Auto-incrementing RoleID
            $table->string('role_name', 50)->unique(); // e.g. "Student", "HOD"
            $table->unsignedTinyInteger('access_level'); // 1=lowest, 8=highest
            $table->timestamps();                  // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};