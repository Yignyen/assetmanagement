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
        Schema::create('assignments', function (Blueprint $table) {
    $table->id();

    // User who receives item
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    // Polymorphic fields
    $table->string('item_type'); // Asset, Accessory, Component
    $table->unsignedBigInteger('item_id');

    $table->date('assigned_at');
    $table->date('returned_at')->nullable();

    $table->enum('status', ['active', 'returned', 'lost'])
          ->default('active');

    // Admin who assigned it
    $table->foreignId('assigned_by')->constrained('users');

    $table->text('notes')->nullable();

    $table->timestamps();

    $table->index(['item_type', 'item_id']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
