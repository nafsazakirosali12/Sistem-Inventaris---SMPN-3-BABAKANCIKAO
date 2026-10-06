<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventories', 'foto')) {
                $table->string('foto')->nullable()->after('nama_barang');
            }
            if (!Schema::hasColumn('inventories', 'nomor_pabrik')) {
                $table->string('nomor_pabrik')->nullable()->after('tahun_pembelian');
            }
            if (!Schema::hasColumn('inventories', 'nomor_rangka')) {
                $table->string('nomor_rangka')->nullable()->after('nomor_pabrik');
            }
            if (!Schema::hasColumn('inventories', 'nomor_mesin')) {
                $table->string('nomor_mesin')->nullable()->after('nomor_rangka');
            }
            if (!Schema::hasColumn('inventories', 'nomor_polisi')) {
                $table->string('nomor_polisi')->nullable()->after('nomor_mesin');
            }
            if (!Schema::hasColumn('inventories', 'nomor_bpkb')) {
                $table->string('nomor_bpkb')->nullable()->after('nomor_polisi');
            }
            if (!Schema::hasColumn('inventories', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('harga');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['foto', 'nomor_pabrik', 'nomor_rangka', 'nomor_mesin', 'nomor_polisi', 'nomor_bpkb', 'deskripsi'] as $col) {
                if (Schema::hasColumn('inventories', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
