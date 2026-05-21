<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('boyongs', function (Blueprint $table) {
            $table->string('nomor_surat')->nullable()->unique()->after('status');
            $table->string('public_token', 80)->nullable()->unique()->after('nomor_surat');
            $table->foreignId('approved_by')->nullable()->after('catatan_pusat')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('boyongs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['nomor_surat', 'public_token']);
        });
    }
};
