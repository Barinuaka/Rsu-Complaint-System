<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                                              // Auto CategoryID
            $table->string('category_name', 100)->unique();           // e.g. "Academic"
            $table->text('category_description')->nullable();         // What this category covers
            $table->unsignedBigInteger('parent_category_id')          // For subcategories
                  ->nullable();                                       // null = top-level category
            $table->boolean('is_active')->default(true);              // Can disable a category
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};