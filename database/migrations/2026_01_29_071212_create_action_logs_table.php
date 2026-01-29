<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('action_logs', function (Blueprint $table) {
            $table->id();

            // Who performed the action
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // What action happened
            $table->string('action_type'); // checkout, checkin, install, assign, etc.

            // The item being acted on (Asset / Accessory / Component)
            $table->string('item_type');
            $table->unsignedBigInteger('item_id');

            // The target of the action (User / Asset / etc.)
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();

            // Extra info
            $table->text('note')->nullable();
            $table->integer('quantity')->default(1);
            $table->timestamp('action_date')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['item_type', 'item_id']);
            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_logs');
    }
};
