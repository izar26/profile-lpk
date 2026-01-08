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
        // 1. UPDATE TABEL EMPLOYEES
        Schema::table('employees', function (Blueprint $table) {
            // Hapus kolom yang tidak relevan / akan diganti
            $table->dropColumn(['linkedin', 'pendidikan_terakhir']);

            // Tambah Kolom Identitas & Fisik (Mirip Student)
            $table->string('nomor_ktp', 16)->nullable()->unique()->after('nip');
            $table->string('nomor_kk', 16)->nullable()->after('nomor_ktp');
            $table->string('nomor_npwp', 20)->nullable()->after('nomor_kk');

            $table->string('golongan_darah', 5)->nullable()->after('jenis_kelamin');
            $table->integer('tinggi_badan')->nullable()->after('golongan_darah');
            $table->integer('berat_badan')->nullable()->after('tinggi_badan');

            $table->string('status_pernikahan')->nullable()->after('agama');

            // Penyesuaian Alamat (Pisahkan KTP dan Domisili)
            // Rename 'alamat' jadi 'alamat_ktp' agar konsisten, atau buat baru
            $table->renameColumn('alamat', 'alamat_ktp');
            $table->renameColumn('kota', 'kota_ktp');
            $table->renameColumn('provinsi', 'provinsi_ktp');
            // $table->renameColumn('kode_pos', 'kode_pos_ktp'); // Jika mau spesifik

            // Tambah Alamat Domisili
            $table->text('alamat_domisili')->nullable()->after('kode_pos');

            // Kontak Tambahan
            $table->string('no_hp_keluarga_darurat', 20)->nullable()->after('telepon'); // Kontak darurat
        });

        // 2. BUAT TABEL RIWAYAT PENDIDIKAN PEGAWAI
        Schema::create('employee_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('jenjang'); // SD, SMP, SMA, D3, S1, S2, dll
            $table->string('nama_sekolah'); // Nama Sekolah / Universitas
            $table->string('jurusan')->nullable();
            $table->string('tahun_masuk', 4)->nullable();
            $table->string('tahun_lulus', 4)->nullable();
            $table->string('nilai_akhir')->nullable(); // IPK atau Nilai Rata-rata
            $table->timestamps();
        });

        // 3. BUAT TABEL DATA KELUARGA PEGAWAI
        Schema::create('employee_families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('hubungan'); // Istri, Suami, Anak, Ayah, Ibu
            $table->string('nama_lengkap');
            $table->string('nik', 16)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('no_hp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus tabel relasi dulu
        Schema::dropIfExists('employee_families');
        Schema::dropIfExists('employee_educations');

        // Rollback tabel employees
        Schema::table('employees', function (Blueprint $table) {
            // Balikkan nama kolom alamat
            $table->renameColumn('alamat_ktp', 'alamat');
            $table->renameColumn('kota_ktp', 'kota');
            $table->renameColumn('provinsi_ktp', 'provinsi');

            // Hapus kolom baru
            $table->dropColumn([ 
                'nomor_ktp', 'nomor_kk', 'nomor_npwp',
                'golongan_darah', 'tinggi_badan', 'berat_badan',
                'status_pernikahan', 'alamat_domisili', 'no_hp_keluarga_darurat'
            ]);

            // Kembalikan kolom lama
            $table->string('linkedin')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
        });
    }
};
