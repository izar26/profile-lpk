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
        Schema::create('galeris', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('album_id')->nullable()->index('galeris_album_id_foreign');
            $table->string('judul')->nullable();
            $table->enum('tipe', ['foto', 'video']);
            $table->string('path_file')->nullable();
            $table->string('url_video')->nullable();
            $table->date('tanggal_kegiatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galeris');
    }
};
