<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boyongs', function (Blueprint $table) {
            if (! Schema::hasColumn('boyongs', 'cut_off_rows')) {
                $table->json('cut_off_rows')->nullable()->after('cut_off_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('boyongs', function (Blueprint $table) {
            if (Schema::hasColumn('boyongs', 'cut_off_rows')) {
                $table->dropColumn('cut_off_rows');
            }
        });
    }
};
