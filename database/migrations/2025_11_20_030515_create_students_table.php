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
    Schema::create('students', function (Blueprint $table) {
        $table->id();
        // Relasi ke User (untuk login nanti)
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

        // Relasi ke Program yang diambil
        $table->foreignId('program_pelatihan_id')->nullable()->constrained('program_pelatihans')->onDelete('set null');

        $table->string('NIK')->nullable()->unique(); // Atau No. KTP
        $table->string('nama');
        $table->string('email')->unique()->nullable();
        $table->string('telepon')->nullable();
        $table->text('alamat')->nullable();
        $table->string('tempat_lahir')->nullable();
        $table->date('tanggal_lahir')->nullable();
        $table->enum('jenis_kelamin', ['L', 'P'])->nullable();

        // Status Perjalanan Siswa
        $table->enum('status', [
            'Mendaftar',    // Baru masuk
            'Wawancara',    // Proses seleksi
            'Pelatihan',    // Sedang belajar
            'Magang',       // Sedang OJT
            'Kerja',        // Sudah ditempatkan kerja
            'Alumni',       // Sudah selesai kontrak/lulus
            'Keluar'        // Drop out
        ])->default('Mendaftar');

        $table->string('foto')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
