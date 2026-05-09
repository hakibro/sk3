<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('boyongs', function (Blueprint $table) {
            $table->id();
            $table->string('idperson', 50)->index(); // Foreign key ke person
            $table->unsignedBigInteger('user_id');    // Pengurus yang mengajukan
            $table->string('asrama_asal')->nullable();
            $table->text('alasan');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_pusat')->nullable(); // Alasan jika ditolak
            $table->timestamp('tgl_disetujui')->nullable();
            $table->timestamps();

            // Relasi ke tabel users Laravel
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boyongs');
    }
};