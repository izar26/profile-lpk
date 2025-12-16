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
    Schema::create('student_documents', function (Blueprint $table) {
        $table->id();
        // Relasi ke Siswa
        $table->unsignedBigInteger('student_id');
        
        // Relasi ke Jenis Dokumen (Master Data)
        $table->unsignedBigInteger('document_type_id');
        
        // Path File
        $table->string('file_path');
        
        $table->timestamps();

        // Foreign Keys
        $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        $table->foreign('document_type_id')->references('id')->on('ref_document_types')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
