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
        Schema::create('lpk_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_lpk')->default('Nama LPK Anda');
            $table->string('nama_pimpinan')->nullable();
            $table->string('nomor_sk')->nullable();
            $table->string('logo')->nullable();
            $table->string('gambar_hero')->nullable();
            $table->string('gambar_tentang')->nullable();
            $table->string('gambar_auth')->nullable();
            $table->string('background_kartu')->nullable();
            $table->text('deskripsi_singkat')->nullable();
            $table->string('tagline')->nullable();
            $table->text('alamat')->nullable();
            $table->text('google_map_embed')->nullable();
            $table->string('email_lpk')->nullable();
            $table->string('website_url')->nullable();
            $table->string('telepon_lpk')->nullable();
            $table->string('nomor_wa')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->timestamps();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('youtube_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpk_profiles');
    }
};
