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
    Schema::create('ref_document_types', function (Blueprint $table) {
        $table->id();
        $table->string('nama'); // Contoh: "Kartu Vaksin"
        $table->string('slug')->unique(); // contoh: "kartu-vaksin"
        $table->boolean('is_required')->default(true); // Apakah Wajib?
        $table->boolean('is_active')->default(true); // Apakah tampil di form siswa?
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_document_types');
    }
};
