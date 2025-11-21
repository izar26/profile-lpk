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
    Schema::table('students', function (Blueprint $table) {
        // Data Pribadi Tambahan
        $table->string('agama')->nullable()->after('jenis_kelamin');
        $table->string('golongan_darah')->nullable()->after('agama');
        
        // Data Keluarga
        $table->string('nama_ayah')->nullable()->after('foto');
        $table->string('nama_ibu')->nullable()->after('nama_ayah');
        $table->string('pekerjaan_ayah')->nullable()->after('nama_ibu');
        $table->string('pekerjaan_ibu')->nullable()->after('pekerjaan_ayah');
        $table->string('no_hp_ortu')->nullable()->after('pekerjaan_ibu');

        // Data Asal Sekolah
        $table->string('sekolah_asal')->nullable()->after('no_hp_ortu');
        $table->string('tahun_lulus')->nullable()->after('sekolah_asal');

        // Data Alamat Detail
        $table->string('kota')->nullable()->after('alamat');
        $table->string('provinsi')->nullable()->after('kota');
        $table->string('kode_pos')->nullable()->after('provinsi');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            //
        });
    }
};
