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

        DB::statement("ALTER TABLE bonsais MODIFY size ENUM('Small', 'Medium', 'Large', 'Mame') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (DB::table('bonsais')->where('size', 'Mame')->exists()) {
            throw new RuntimeException('Cannot remove Mame while bonsais still use it.');
        }

        DB::statement("ALTER TABLE bonsais MODIFY size ENUM('Small', 'Medium', 'Large') NOT NULL");
    }
};
