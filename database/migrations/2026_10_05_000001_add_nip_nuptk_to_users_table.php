<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'nip_nuptk')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nip_nuptk')->nullable()->after('username')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'nip_nuptk')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nip_nuptk');
            });
        }
    }
};
