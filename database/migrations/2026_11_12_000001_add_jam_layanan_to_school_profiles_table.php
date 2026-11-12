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
        Schema::table('school_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('school_profiles', 'hari_layanan')) {
                $table->string('hari_layanan')->nullable()->default('Senin s.d. Jumat');
            }
            if (!Schema::hasColumn('school_profiles', 'jam_buka')) {
                $table->string('jam_buka', 10)->nullable()->default('07:00');
            }
            if (!Schema::hasColumn('school_profiles', 'jam_tutup')) {
                $table->string('jam_tutup', 10)->nullable()->default('13:00');
            }
            if (!Schema::hasColumn('school_profiles', 'hari_operasional')) {
                $table->text('hari_operasional')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('school_profiles', 'hari_layanan')) {
                $columnsToDrop[] = 'hari_layanan';
            }
            if (Schema::hasColumn('school_profiles', 'jam_buka')) {
                $columnsToDrop[] = 'jam_buka';
            }
            if (Schema::hasColumn('school_profiles', 'jam_tutup')) {
                $columnsToDrop[] = 'jam_tutup';
            }
            if (Schema::hasColumn('school_profiles', 'hari_operasional')) {
                $columnsToDrop[] = 'hari_operasional';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

