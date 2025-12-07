<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_experiences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id')->index();
            
            // Pembeda Riwayat Pekerjaan [cite: 32] vs Pengalaman Organisasi [cite: 23]
            $table->enum('tipe', ['Pekerjaan', 'Organisasi'])->default('Pekerjaan');
            
            // Nama Perusahaan [cite: 33] / Organisasi 
            $table->string('nama_instansi');
            
            // Jenis Usaha [cite: 34] / Bidang 
            $table->string('jenis_usaha')->nullable();
            
            // Alamat Perusahaan [cite: 35] / Tempat (Organisasi) 
            $table->text('alamat_instansi')->nullable();
            
            // Posisi [cite: 36] / Jabatan 
            $table->string('posisi');
            
            // Lama Bekerja (Dari... Sampai...) [cite: 36] / Waktu (Organisasi) 
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            
            // Gaji Awal [cite: 37] (Khusus Pekerjaan)
            $table->string('gaji_awal')->nullable();
            
            // Gaji Akhir [cite: 37] (Khusus Pekerjaan)
            $table->string('gaji_akhir')->nullable();
            
            // Alasan Berhenti [cite: 38] (Khusus Pekerjaan)
            $table->text('alasan_berhenti')->nullable();
            
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_experiences');
    }
};