<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('boyongs', 'public_token')) {
            Schema::table('boyongs', function (Blueprint $table) {
                $table->string('public_token', 80)->nullable()->unique()->after('nomor_surat');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('boyongs', 'public_token')) {
            Schema::table('boyongs', function (Blueprint $table) {
                $table->dropColumn('public_token');
            });
        }
    }
};
