<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boyongs', function (Blueprint $table) {
            if (! Schema::hasColumn('boyongs', 'boyong_scope')) {
                $table->json('boyong_scope')->nullable()->after('snapshot_tagihan');
            }

            if (! Schema::hasColumn('boyongs', 'cut_off_status')) {
                $table->string('cut_off_status')->default('belum')->after('boyong_scope');
            }

            if (! Schema::hasColumn('boyongs', 'cut_off_at')) {
                $table->timestamp('cut_off_at')->nullable()->after('cut_off_status');
            }

            if (! Schema::hasColumn('boyongs', 'cut_off_by')) {
                $table->unsignedBigInteger('cut_off_by')->nullable()->after('cut_off_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('boyongs', function (Blueprint $table) {
            foreach (['cut_off_by', 'cut_off_at', 'cut_off_status', 'boyong_scope'] as $column) {
                if (Schema::hasColumn('boyongs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
