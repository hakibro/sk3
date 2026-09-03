<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'boyong_scope',
        'cut_off_status',
        'cut_off_at',
        'cut_off_by',
        'cut_off_rows',
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
        'boyong_scope' => 'array',
        'cut_off_rows' => 'array',
        'tgl_disetujui' => 'datetime',
        'cut_off_at' => 'datetime',
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

    /**
     * Daftar label cakupan boyong yang aktif (Asrama/Madin/Formal).
     */
    public function getScopeLabelsAttribute(): array
    {
        $scope = $this->boyong_scope ?: [];

        $labels = [];
        if (! empty($scope['asrama'])) {
            $labels[] = 'Asrama';
        }
        if (! empty($scope['madin'])) {
            $labels[] = 'Madin';
        }
        if (! empty($scope['formal'])) {
            $labels[] = 'Formal';
        }

        return $labels;
    }

    /**
     * Ringkasan cakupan boyong dalam satu string, mis. "Asrama + Madin".
     */
    public function getScopeSummaryAttribute(): string
    {
        $labels = $this->getScopeLabelsAttribute();

        return $labels ? implode(' + ', $labels) : 'Asrama';
    }
}
