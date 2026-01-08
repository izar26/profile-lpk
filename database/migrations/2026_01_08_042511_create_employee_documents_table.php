<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            // Terhubung ke pegawai
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            // INI KUNCINYA: String bebas, bukan integer/ID
            $table->string('nama_dokumen');

            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
