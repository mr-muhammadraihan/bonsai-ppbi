<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->dropUnique('bonsais_bonsai_code_unique');
        });

        DB::table('bonsais')
            ->whereNotNull('bonsai_type_id')
            ->orderBy('id')
            ->eachById(function (object $bonsai): void {
                $name = DB::table('bonsai_types')->where('id', $bonsai->bonsai_type_id)->value('name');

                if ($name) {
                    DB::table('bonsais')->where('id', $bonsai->id)->update(['bonsai_type' => $name]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bonsais', function (Blueprint $table): void {
            $table->dropColumn('bonsai_type');
        });
    }
};
