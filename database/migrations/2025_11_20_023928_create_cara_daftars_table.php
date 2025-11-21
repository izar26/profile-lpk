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
    Schema::create('cara_daftars', function (Blueprint $table) {
        $table->id();
        $table->string('judul'); // Misal: "Isi Formulir"
        $table->text('deskripsi'); // Misal: "Lengkapi data diri..."
        $table->integer('urutan')->default(1); // Untuk mengatur urutan langkah
        $table->string('gambar')->nullable(); // Ikon atau ilustrasi langkah
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cara_daftars');
    }
};
