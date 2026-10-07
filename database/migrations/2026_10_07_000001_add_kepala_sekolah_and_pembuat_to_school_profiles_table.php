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
            if (!Schema::hasColumn('school_profiles', 'nama_kepala_sekolah')) {
                $table->string('nama_kepala_sekolah')->nullable();
            }
            if (!Schema::hasColumn('school_profiles', 'nip_kepala_sekolah')) {
                $table->string('nip_kepala_sekolah')->nullable();
            }
            if (!Schema::hasColumn('school_profiles', 'nama_pembuat')) {
                $table->string('nama_pembuat')->nullable();
            }
            if (!Schema::hasColumn('school_profiles', 'nip_pembuat')) {
                $table->string('nip_pembuat')->nullable();
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
            if (Schema::hasColumn('school_profiles', 'nama_kepala_sekolah')) {
                $columnsToDrop[] = 'nama_kepala_sekolah';
            }
            if (Schema::hasColumn('school_profiles', 'nip_kepala_sekolah')) {
                $columnsToDrop[] = 'nip_kepala_sekolah';
            }
            if (Schema::hasColumn('school_profiles', 'nama_pembuat')) {
                $columnsToDrop[] = 'nama_pembuat';
            }
            if (Schema::hasColumn('school_profiles', 'nip_pembuat')) {
                $columnsToDrop[] = 'nip_pembuat';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
