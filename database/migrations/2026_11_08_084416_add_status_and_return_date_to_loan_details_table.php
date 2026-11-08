<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loan_details', function (Blueprint $table) {
            if (!Schema::hasColumn('loan_details', 'status')) {
                $table->string('status')->default('dipinjam')->after('kode_barang');
            }
            if (!Schema::hasColumn('loan_details', 'tanggal_kembali_aktual')) {
                $table->timestamp('tanggal_kembali_aktual')->nullable()->after('status');
            }
        });

        // Backfill data from parent loans table
        if (Schema::hasTable('loans')) {
            $loans = DB::table('loans')->select('id', 'status', 'tanggal_kembali_aktual')->get();
            foreach ($loans as $loan) {
                DB::table('loan_details')
                    ->where('loan_id', $loan->id)
                    ->update([
                        'status' => $loan->status ?? 'dipinjam',
                        'tanggal_kembali_aktual' => $loan->tanggal_kembali_aktual,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_details', function (Blueprint $table) {
            if (Schema::hasColumn('loan_details', 'tanggal_kembali_aktual')) {
                $table->dropColumn('tanggal_kembali_aktual');
            }
            if (Schema::hasColumn('loan_details', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
