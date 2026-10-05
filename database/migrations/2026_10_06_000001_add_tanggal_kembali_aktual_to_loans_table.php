<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'tanggal_kembali_aktual')) {
                $table->dateTime('tanggal_kembali_aktual')->nullable()->after('tanggal_kembali');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (Schema::hasColumn('loans', 'tanggal_kembali_aktual')) {
                $table->dropColumn('tanggal_kembali_aktual');
            }
        });
    }
};
