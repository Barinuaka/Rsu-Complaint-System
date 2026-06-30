<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anonymous_vaults', function (Blueprint $table) {
            $table->id();

            // Links to the complaint — but this is the ONLY link
            $table->unsignedBigInteger('complaint_id')->unique(); // One vault per complaint

            // AES-256-GCM encrypted identity data
            $table->binary('encrypted_identity');                // The encrypted blob
            $table->string('encryption_key_ref', 100);          // Which key was used
            $table->string('authentication_tag', 100);          // GCM integrity tag

            // Legal disclosure audit trail
            $table->text('disclosure_log')->nullable();          // Records any legal disclosures

            $table->timestamp('vault_created_at');               // When vault was sealed
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anonymous_vaults');
    }
};