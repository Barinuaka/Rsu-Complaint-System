<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                                     // Auto UserID
            $table->string('full_name', 150);                                 // Full name
            $table->string('matric_number', 20)->nullable()->unique();        // e.g. DE.2022/7545
            $table->string('email')->unique();                                // Institutional email
            $table->string('phone_number', 20)->nullable();                   // Phone for SMS alerts
            $table->string('password');                                       // bcrypt hashed
            $table->unsignedBigInteger('role_id');                            // FK → roles
            $table->unsignedBigInteger('campus_id')->nullable();              // FK → campuses
            $table->string('department', 100)->nullable();                    // e.g. "Computer Science"
            $table->enum('account_status', ['active','inactive','suspended']) // Account state
                  ->default('active');
            $table->timestamp('email_verified_at')->nullable();               // Email verification
            $table->timestamp('last_login_at')->nullable();                   // Track last login
            $table->rememberToken();                                          // Laravel "remember me"
            $table->timestamps();                                             // created_at, updated_at

            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};