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
        Schema::table('users', function (Blueprint $table) {

            // 1️⃣ Add location_id (department reference)
            $table->integer('location_id')
                  ->nullable()
                  ->index()
                  ->after('role');

            // 2️⃣ Remove department string column
            if (Schema::hasColumn('users', 'department')) {
                $table->dropColumn('department');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Rollback: restore department column
            $table->string('department')->nullable();

            // Remove location_id
            $table->dropColumn('location_id');
        });
    
    }
};
