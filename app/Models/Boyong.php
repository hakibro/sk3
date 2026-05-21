<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Boyong extends Model
{
    use HasFactory;

    protected $fillable = [
        'idperson',
        'user_id',
        'asrama_asal',
        'tanggal_boyong',
        'alasan',
        'alasan_kategori',
        'alasan_detail',
        'status',
        'catatan_pusat',
        'pembayaran_belum_lunas',
        'total_tagihan_saat_pengajuan',
        'kos_makan_bulan_berjalan',
        'spp_bulan_berjalan_full',
        'status_cut_pembayaran',
        'snapshot_tagihan',
        'tgl_disetujui',
        'approved_by',
        'nomor_surat',
        'public_token',
    ];

    protected $casts = [
        'tanggal_boyong' => 'date',
        'pembayaran_belum_lunas' => 'boolean',
        'spp_bulan_berjalan_full' => 'boolean',
        'snapshot_tagihan' => 'array',
        'tgl_disetujui' => 'datetime',
    ];

    /**
     * Relasi ke data Siswa (View)
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'idperson', 'idperson');
    }

    /**
     * Relasi ke User (Pengurus yang mengajukan)
     */
    public function pengurus()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope untuk mempermudah filter status
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
