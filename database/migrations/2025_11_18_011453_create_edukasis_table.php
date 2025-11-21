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
    Schema::create('edukasis', function (Blueprint $table) {
        $table->id();
        $table->string('judul');
        $table->string('slug')->unique(); // Untuk URL yang cantik
        $table->longText('konten'); // Ini akan menyimpan konten dari Trix
        $table->string('gambar_fitur')->nullable();
        $table->enum('status', ['Published', 'Draft'])->default('Draft');

        // Kolom untuk 'Penulis'
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edukasis');
    }
};
