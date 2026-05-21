<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('siswa_status_pembayaran')) {
            Schema::create('siswa_status_pembayaran', function (Blueprint $table) {
                $table->string('idperson', 50)->primary();
                $table->decimal('total_tunggakan', 15, 2)->default(0);
                $table->boolean('is_lunas')->default(true)->index();
                $table->timestamp('refreshed_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_status_pembayaran');
    }
};
