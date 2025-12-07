<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnis', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Relasi ke tabel students (otomatis mencari student_id)
            // onDelete cascade artinya jika data student dihapus, testimoni juga terhapus
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            
            $table->string('kerja_dimana');
            $table->string('angkatan')->nullable();
            $table->text('testimoni');
            $table->string('foto')->nullable(); // Foto khusus testimoni (opsional, beda dgn foto profil)
            
            // Status untuk moderasi (0 = Draft/Hidden, 1 = Tampil/Published)
            $table->boolean('is_published')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnis');
    }
};