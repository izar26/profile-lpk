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
    Schema::create('employees', function (Blueprint $table) {
        $table->id();
        // Relasi ke User (jika pegawai ini punya akses login, misal sebagai Admin/Guru)
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

        $table->string('nip')->nullable()->unique(); // Nomor Induk Pegawai
        $table->string('nama');
        $table->string('jabatan'); // Contoh: "Instruktur Las", "Admin", "Kepala LPK"
        $table->string('email')->nullable();
        $table->string('telepon')->nullable();
        $table->text('alamat')->nullable();
        $table->string('foto')->nullable();

        // Link sosmed (opsional, bagus untuk profil instruktur)
        $table->string('linkedin')->nullable();
        $table->string('instagram')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
