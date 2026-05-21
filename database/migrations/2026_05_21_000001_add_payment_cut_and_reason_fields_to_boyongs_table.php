<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('boyongs', function (Blueprint $table) {
            if (! Schema::hasColumn('boyongs', 'tanggal_boyong')) {
                $table->date('tanggal_boyong')->nullable()->after('asrama_asal');
            }

            if (! Schema::hasColumn('boyongs', 'alasan_kategori')) {
                $table->string('alasan_kategori')->nullable()->after('alasan');
            }

            if (! Schema::hasColumn('boyongs', 'alasan_detail')) {
                $table->text('alasan_detail')->nullable()->after('alasan_kategori');
            }

            if (! Schema::hasColumn('boyongs', 'pembayaran_belum_lunas')) {
                $table->boolean('pembayaran_belum_lunas')->default(false)->after('catatan_pusat');
            }

            if (! Schema::hasColumn('boyongs', 'total_tagihan_saat_pengajuan')) {
                $table->unsignedBigInteger('total_tagihan_saat_pengajuan')->default(0)->after('pembayaran_belum_lunas');
            }

            if (! Schema::hasColumn('boyongs', 'kos_makan_bulan_berjalan')) {
                $table->unsignedBigInteger('kos_makan_bulan_berjalan')->default(0)->after('total_tagihan_saat_pengajuan');
            }

            if (! Schema::hasColumn('boyongs', 'spp_bulan_berjalan_full')) {
                $table->boolean('spp_bulan_berjalan_full')->default(false)->after('kos_makan_bulan_berjalan');
            }

            if (! Schema::hasColumn('boyongs', 'status_cut_pembayaran')) {
                $table->string('status_cut_pembayaran')->default('tidak_perlu')->after('spp_bulan_berjalan_full');
            }

            if (! Schema::hasColumn('boyongs', 'snapshot_tagihan')) {
                $table->json('snapshot_tagihan')->nullable()->after('status_cut_pembayaran');
            }
        });
    }

    public function down(): void
    {
        Schema::table('boyongs', function (Blueprint $table) {
            foreach ([
                'snapshot_tagihan',
                'status_cut_pembayaran',
                'spp_bulan_berjalan_full',
                'kos_makan_bulan_berjalan',
                'total_tagihan_saat_pengajuan',
                'pembayaran_belum_lunas',
                'alasan_detail',
                'alasan_kategori',
                'tanggal_boyong',
            ] as $column) {
                if (Schema::hasColumn('boyongs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
