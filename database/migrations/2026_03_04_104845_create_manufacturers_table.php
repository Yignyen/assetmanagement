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
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();

        // Optional useful fields
            $table->string('support_url')->nullable();
            $table->string('warranty_lookup_url')->nullable();
            $table->string('support_phone')->nullable();
            $table->string('support_email')->nullable();
            $table->softDeletes(); // same as Snipe-IT

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturers');
    }
};
