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
        Schema::create('program_pelatihans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('judul');
            $table->text('deskripsi_singkat');
            $table->text('deskripsi_lengkap')->nullable();
            $table->string('gambar_fitur')->nullable();
            $table->enum('status', ['Akan Datang', 'Buka Pendaftaran', 'Berjalan', 'Selesai'])->default('Akan Datang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_pelatihans');
    }
};
