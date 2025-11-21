<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lpk_profiles', function (Blueprint $table) {
            $table->id(); // ID-nya akan selalu '1'
            $table->string('nama_lpk')->default('Nama LPK Anda');
            $table->string('logo')->nullable(); // Path ke file logo
            $table->text('deskripsi_singkat')->nullable();
            $table->text('alamat')->nullable();
            $table->string('email_lpk')->nullable();
            $table->string('telepon_lpk')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lpk_profiles');
    }
};