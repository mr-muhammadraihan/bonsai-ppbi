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
        Schema::table('bonsais', function (Blueprint $table): void {
            $table->string('bonsai_type')->nullable()->after('participant_id');
            $table->text('photo')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bonsais', function (Blueprint $table): void {
            $table->dropColumn('bonsai_type');
            $table->text('photo')->nullable(false)->change();
        });
    }
};
