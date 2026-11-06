<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add jumlah column to loans table if not exists
        if (!Schema::hasColumn('loans', 'jumlah')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->integer('jumlah')->default(1)->after('inventory_id');
            });
        }

        // 2. Create loan_details table
        if (!Schema::hasTable('loan_details')) {
            Schema::create('loan_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
                $table->foreignId('inventory_id')->nullable()->constrained('inventories')->onDelete('set null');
                $table->string('kode_barang');
                $table->timestamps();
            });
        }

        // 3. Backfill loan_details for existing loans
        if (Schema::hasTable('loans') && Schema::hasTable('inventories')) {
            $existingLoans = DB::table('loans')
                ->join('inventories', 'loans.inventory_id', '=', 'inventories.id')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('loan_details')
                        ->whereColumn('loan_details.loan_id', 'loans.id');
                })
                ->select('loans.id as loan_id', 'inventories.id as inventory_id', 'inventories.kode_barang')
                ->get();

            foreach ($existingLoans as $l) {
                DB::table('loan_details')->insert([
                    'loan_id' => $l->loan_id,
                    'inventory_id' => $l->inventory_id,
                    'kode_barang' => $l->kode_barang,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_details');

        if (Schema::hasColumn('loans', 'jumlah')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->dropColumn('jumlah');
            });
        }
    }
};

