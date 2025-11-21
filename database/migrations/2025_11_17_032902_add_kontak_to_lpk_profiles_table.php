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
    Schema::table('lpk_profiles', function (Blueprint $table) {
        // 1. Tagline
        $table->string('tagline')->nullable()->after('deskripsi_singkat');

        // 2. Kontak & Lokasi
        $table->text('google_map_embed')->nullable()->after('alamat');
        $table->string('nomor_wa')->nullable()->after('telepon_lpk');
        $table->string('website_url')->nullable()->after('email_lpk');

        // 3. Medsos
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
        Schema::table('lpk_profiles', function (Blueprint $table) {
            //
        });
    }
};
