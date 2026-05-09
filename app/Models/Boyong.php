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
        'alasan',
        'status',
        'catatan_pusat',
        'tgl_disetujui'
    ];

    protected $casts = [
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