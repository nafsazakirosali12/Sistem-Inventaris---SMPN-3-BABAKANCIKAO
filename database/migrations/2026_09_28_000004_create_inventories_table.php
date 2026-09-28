<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('restrict');
            $table->string('status')->default('baik'); // baik, dipinjam, rusak
            $table->string('nomor_register')->nullable();
            $table->string('merk_type')->nullable();
            $table->string('ukuran_cc')->nullable();
            $table->string('bahan')->nullable();
            $table->string('tahun_pembelian')->nullable();
            $table->string('asal_usul')->nullable();
            $table->decimal('harga', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
