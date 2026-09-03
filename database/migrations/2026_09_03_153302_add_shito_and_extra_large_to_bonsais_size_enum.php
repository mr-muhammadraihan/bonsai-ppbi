<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE bonsais MODIFY size ENUM('Small', 'Medium', 'Large', 'Mame', 'Shito', 'Extra Large') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (DB::table('bonsais')->whereIn('size', ['Shito', 'Extra Large'])->exists()) {
            throw new RuntimeException('Cannot remove Shito or Extra Large while bonsais still use them.');
        }

        DB::statement("ALTER TABLE bonsais MODIFY size ENUM('Small', 'Medium', 'Large', 'Mame') NOT NULL");
    }
};
