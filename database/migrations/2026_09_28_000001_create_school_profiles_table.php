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
        Schema::create('school_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah')->default('SMPN 3 BABAKANCIKAO');
            $table->string('npsn')->default('20203040');
            $table->string('nama_sistem')->default('Sistem Informasi Inventaris & Peminjaman');
            $table->string('tagline')->default('Portal Terpadu Sarana & Prasarana');
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->string('foto')->nullable();
            $table->string('denah')->nullable();
            $table->string('provinsi')->default('Jawa Barat');
            $table->string('kabupaten_kota')->default('Kabupaten Purwakarta');
            $table->string('bidang')->default('Pendidikan');
            $table->string('unit_organisasi')->default('Dinas Pendidikan');
            $table->string('sub_unit_organisasi')->default('SMPN 3 Babakancikao');
            $table->string('upb')->default('SMPN 3 Babakancikao');
            $table->string('no_kode_lokasi')->default('12.34.56.78.90');
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('facebook')->nullable();
            $table->string('tiktok')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_profiles');
    }
};
