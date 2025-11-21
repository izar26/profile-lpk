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
    Schema::create('keberangkatans', function (Blueprint $table) {
        $table->id();
        $table->string('judul'); // Misal: "Pelepasan Angkatan 12 ke Saitama"
        $table->string('tujuan'); // Misal: "Saitama, Jepang"
        $table->date('tanggal_berangkat');
        $table->integer('jumlah_peserta'); // Misal: 15
        $table->text('deskripsi')->nullable();
        $table->string('foto')->nullable(); // Foto wajib (Group photo)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keberangkatans');
    }
};
