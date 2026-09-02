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
        Schema::create('bonsais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');
            $table->string('bonsai_code')->unique();
            $table->enum('size', ['Small', 'Medium', 'Large']);
            $table->enum('class', ['Jadi', 'Prospek']);
            $table->enum('status', ['Peserta', 'Pemenang']);
            $table->string('predicate')->nullable();
            $table->text('description')->nullable();
            $table->text('photo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonsais');
    }
};
