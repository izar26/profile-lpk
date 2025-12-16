<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            // WAJIB ADA (Sesuai Request)
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('program_pelatihan_id')->nullable()->index();
            
            // --- [HEADER] ---
            // Pas Foto Ukuran 3,5 x 4,5 [cite: 1]
            $table->string('foto')->nullable(); 

            // --- [DATA PRIBADI] ---
            // Nama Lengkap Sesuai KTP [cite: 6]
            $table->string('nama_lengkap'); 
            
            // Tempat, Tanggal Lahir [cite: 7]
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            
            // Jenis Kelamin [cite: 8]
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            
            // Tinggi Badan & Berat Badan [cite: 9]
            $table->integer('tinggi_badan')->nullable();
            $table->integer('berat_badan')->nullable();
            
            // Alamat Sesuai KTP [cite: 10]
            $table->text('alamat_ktp')->nullable(); 
            // (Optional: Helper untuk Kota/Provinsi KTP agar data rapi)
            $table->string('kota_ktp')->nullable();
            $table->string('provinsi_ktp')->nullable();

            // Alamat Domisili [cite: 12]
            $table->text('alamat_domisili')->nullable();
            
            // Status Pernikahan [cite: 15-17]
            $table->enum('status_pernikahan', ['Belum Menikah', 'Menikah', 'Janda/Duda'])->nullable();

            // --- [DATA TAMBAHAN - TABEL KECIL] [cite: 18] ---
            $table->string('agama')->nullable();
            $table->string('nomor_ktp')->nullable()->unique();
            // Nomor NPWP dan Paspor [cite: 18, 82]
            $table->string('nomor_paspor')->nullable(); 
            $table->string('nomor_npwp')->nullable();   
            $table->string('email')->nullable()->unique();
            $table->string('golongan_darah')->nullable();
            
            // Nomor Telepon (Peserta & Orang Tua) [cite: 18]
            $table->string('no_hp_peserta')->nullable(); 
            $table->string('no_hp_ortu')->nullable();

            // --- [PERTANYAAN KHUSUS] ---
            // Apakah Anda Pernah Bekerja? (Ya/Tidak) [cite: 29-31]
            $table->boolean('pernah_bekerja')->default(false);
            

            // --- [BAGIAN TANDA TANGAN] ---
            // Mengakomodasi isian "Kota........, Tgl....." di atas tanda tangan [cite: 62, 69]
            $table->string('kota_pembuatan')->nullable(); 

            // --- [SYSTEM STATUS] ---
            $table->enum('status', [
                'Mendaftar', 'Menunggu Verifikasi', 'Perlu Revisi', 
                'Ditolak', 'Wawancara', 'Pelatihan', 
                'Magang', 'Kerja', 'Alumni', 'Keluar'
            ])->default('Mendaftar');
            
            $table->text('admin_note')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};