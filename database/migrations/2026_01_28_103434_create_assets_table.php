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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Asset name (e.g. Dell Laptop)
            $table->string('serial_no')->unique(); // Unique serial number
            $table->string('asset_tag')->unique(); // Company asset tag
            $table->enum('status', ['available', 'assigned', 'broken'])->default('available');
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->date('purchase_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
