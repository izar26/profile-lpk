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
    Schema::table('galeris', function (Blueprint $table) {
        // 1. Jadikan 'judul' opsional (sesuai permintaan Anda)
        $table->string('judul')->nullable()->change();

        // 2. Tambahkan 'album_id' sebagai "Koneksi Wajib"
        // Kita buat 'nullable' dulu agar foto-foto lama Anda tidak error.
        // Nanti di controller, kita akan WAJIBKAN user memilih album.
        $table->foreignId('album_id')
              ->nullable()
              ->after('id') // Taruh di dekat 'id'
              ->constrained('albums') // Terhubung ke tabel 'albums'
              ->onDelete('cascade'); // Jika album dihapus, fotonya ikut terhapus
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galeris', function (Blueprint $table) {
            //
        });
    }
};
