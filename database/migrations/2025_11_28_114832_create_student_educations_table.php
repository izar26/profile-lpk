<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_educations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id')->index();
            
            // Kategori untuk membedakan Tabel Formal [cite: 19] & Non-Formal [cite: 21]
            $table->enum('kategori', ['Formal', 'Non-Formal'])->default('Formal');
            
            // Tingkat  / Tipe 
            $table->string('tingkat'); 
            
            // Nama Sekolah  / Nama Institusi 
            $table->string('nama_institusi');
            
            // Lokasi  / Tempat 
            $table->string('lokasi')->nullable();
            
            // Jurusan  / Materi 
            $table->string('jurusan')->nullable();
            
            // Tahun Masuk / Dari 
            // Untuk Non-Formal, kolom ini menyimpan "Durasi Waktu" 
            $table->string('tahun_masuk')->nullable(); 
            
            // Tahun Keluar / Sampai 
            $table->string('tahun_lulus')->nullable(); 
            
            // Nilai Rata-Rata  (Khusus Formal)
            $table->string('nilai_rata_rata')->nullable();
            
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_educations');
    }
};