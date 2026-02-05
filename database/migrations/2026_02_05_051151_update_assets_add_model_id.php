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
        Schema::table('assets', function (Blueprint $table) {
             $table->dropForeign(['category_id']);

        // 2️⃣ Drop the column
            $table->dropColumn('category_id');

        // 3️⃣ Add new column
            $table->unsignedBigInteger('model_id')->after('asset_tag');
                    // (optional but recommended)
        // $table->foreign('model_id')->references('id')->on('models');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
        // 1️⃣ Remove model_id (FK first if exists)
        // $table->dropForeign(['model_id']);
        $table->dropColumn('model_id');

        // 2️⃣ Restore category_id
        $table->unsignedBigInteger('category_id')->nullable();

        // 3️⃣ Restore FK
        $table->foreign('category_id')->references('id')->on('categories');
    });
    }
};
