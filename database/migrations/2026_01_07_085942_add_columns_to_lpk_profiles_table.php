<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lpk_profiles', function (Blueprint $table) {
            // Cek dulu apakah kolom sudah ada agar tidak error jika dijalankan ulang

            // Kolom untuk Kop Surat & Background Surat (Permintaan User)
            if (!Schema::hasColumn('lpk_profiles', 'kop_surat')) {
                $table->string('kop_surat', 255)->nullable()->after('nomor_sk'); // Gambar Kop
            }
            if (!Schema::hasColumn('lpk_profiles', 'background_surat')) {
                $table->string('background_surat', 255)->nullable()->after('kop_surat'); // Watermark/Background Kertas
            }

            // Kolom Tambahan sesuai View & Controller update Anda
            if (!Schema::hasColumn('lpk_profiles', 'gambar_auth')) {
                $table->string('gambar_auth', 255)->nullable()->after('gambar_tentang');
            }
            if (!Schema::hasColumn('lpk_profiles', 'background_kartu')) {
                $table->string('background_kartu', 255)->nullable()->after('gambar_auth');
            }
            if (!Schema::hasColumn('lpk_profiles', 'nama_pimpinan')) {
                $table->string('nama_pimpinan', 255)->nullable()->after('nama_lpk');
            }
            if (!Schema::hasColumn('lpk_profiles', 'nomor_sk')) {
                $table->string('nomor_sk', 100)->nullable()->after('nama_pimpinan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lpk_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'kop_surat',
                'background_surat',
                'gambar_auth',
                'background_kartu',
                'nama_pimpinan',
                'nomor_sk'
            ]);
        });
    }
};
