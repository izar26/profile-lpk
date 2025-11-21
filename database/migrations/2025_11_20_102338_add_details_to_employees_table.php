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
    Schema::table('employees', function (Blueprint $table) {
        // Data Pribadi Tambahan
        $table->string('tempat_lahir')->nullable()->after('nama');
        $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
        $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir');
        $table->string('agama')->nullable()->after('jenis_kelamin');
        $table->string('pendidikan_terakhir')->nullable()->after('agama'); // SD/SMP/SMA/S1/S2
        $table->string('status_kepegawaian')->default('Kontrak')->after('jabatan'); // Tetap/Kontrak/Part-time
        
        // Alamat Detail
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
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
};
