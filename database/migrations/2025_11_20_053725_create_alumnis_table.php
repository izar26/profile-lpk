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
    Schema::create('alumnis', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->string('kerja_dimana'); // Contoh: "Osaka - Pabrik Toyota"
        $table->string('angkatan')->nullable(); // Contoh: "Angkatan 2022"
        $table->text('testimoni'); // Kesan pesan
        $table->string('foto')->nullable(); // Foto saat di Jepang
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnis');
    }
};
