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
        Schema::create('accessories_checkout', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('accessory_id');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->string('assigned_type')->nullable();

            $table->unsignedBigInteger('created_by'); // admin who checked out
            $table->text('note')->nullable();

            $table->timestamp('checked_out_at')->useCurrent();
            $table->timestamp('returned_at')->nullable();

            $table->timestamps();

    // Optional indexes (like Snipe-IT style)
            $table->index(['assigned_type', 'assigned_to']);
            $table->index('accessory_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessories_checkout');
    }
};
