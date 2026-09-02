<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $types = [
            'Bonsai Beringin / Ficus', 'Bonsai Cemara (Juniperus)',
            'Bonsai Sisir (Cudrania cochinchinensis)', 'Bonsai Anting Putri (Wrightia religiosa)',
            'Bonsai Santigi (Pemphis acidula)', 'Sancang (Premna)', 'Streblus asper',
            'Bonsai Asam Jawa (Tamarindus indica)', 'Cemara Udang (Casuarina equisetifolia)',
            'Hibiscus (Waru) (Hibiscus tiliaceus)', 'Bougainvillea (Kembang Kertas)',
            'Hokiantea (Carmona)', 'Bonsai Kamboja Jepang (Adenium obesum)',
            'Bonsai Ceri / Sakura Jepang (Prunus)', 'Bonsai Murbei (Morus)', 'Lohansung (Podocarpus)',
        ];

        foreach ($types as $name) {
            DB::table('bonsai_types')->insertOrIgnore([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('bonsais')->whereNotNull('bonsai_type')->distinct()->pluck('bonsai_type')->each(function (string $name): void {
            DB::table('bonsai_types')->insertOrIgnore([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        Schema::table('bonsais', function (Blueprint $table): void {
            $table->foreignId('bonsai_type_id')->nullable()->after('participant_id');
            $table->foreign('bonsai_type_id')->references('id')->on('bonsai_types')->nullOnDelete();
        });

        DB::table('bonsais')->whereNotNull('bonsai_type')->orderBy('id')->eachById(function (object $bonsai): void {
            $typeId = DB::table('bonsai_types')->where('name', $bonsai->bonsai_type)->value('id');

            if ($typeId) {
                DB::table('bonsais')->where('id', $bonsai->id)->update(['bonsai_type_id' => $typeId]);
            }
        });

        Schema::table('bonsais', function (Blueprint $table): void {
            $table->dropColumn('bonsai_type');
        });
    }

    public function down(): void
    {
        Schema::table('bonsais', function (Blueprint $table): void {
            $table->string('bonsai_type')->nullable()->after('participant_id');
        });

        DB::table('bonsais')->whereNotNull('bonsai_type_id')->orderBy('id')->eachById(function (object $bonsai): void {
            $name = DB::table('bonsai_types')->where('id', $bonsai->bonsai_type_id)->value('name');
            DB::table('bonsais')->where('id', $bonsai->id)->update(['bonsai_type' => $name]);
        });

        Schema::table('bonsais', function (Blueprint $table): void {
            $table->dropForeign(['bonsai_type_id']);
            $table->dropColumn('bonsai_type_id');
        });

        Schema::dropIfExists('bonsai_types');
    }
};
