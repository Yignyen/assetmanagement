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
        Schema::create('components_assets', function (Blueprint $table) {
            $table->id();

            // Foreign keys (logical, even if you don't enforce constraints yet)
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('component_id');

            // How many of this component assigned to this asset
            $table->integer('assigned_qty')->default(1);

            // Who did the assignment
            $table->unsignedBigInteger('created_by')->nullable();

            // Optional note
            $table->text('note')->nullable();

            $table->timestamps();

            // Optional but recommended indexes
            $table->index(['asset_id', 'component_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components_assets');
    }
};
