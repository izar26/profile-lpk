<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_families', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id')->index();
            
            // Hubungan Keluarga  (Ayah, Ibu, Saudara 1, dll)
            $table->string('hubungan'); 
            
            // L/P 
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            
            // Nama (Termasuk Gelar) 
            $table->string('nama');
            
            // Tanggal Lahir 
            $table->date('tanggal_lahir')->nullable();
            
            // Usia  - Formulir meminta ini ditulis manual
            $table->integer('usia')->nullable();
            
            // Pendidikan 
            $table->string('pendidikan')->nullable();
            
            // Pekerjaan 
            $table->string('pekerjaan')->nullable();
            
            // Penghasilan Rata-Rata Per Bulan 
            $table->string('penghasilan')->nullable();
            
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_families');
    }
};