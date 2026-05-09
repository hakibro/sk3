<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'v_siswa';
    protected $primaryKey = 'idperson';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /**
     * Relasi ke Asrama
     * v_siswa terhubung ke v_asrama melalui idkelas (foreign key di tbl_siswa asal)
     */
    public function asrama()
    {
        // Karena di view v_siswa mungkin tidak ada idkelas secara eksplisit (hanya ada asrama/kamar),
        // pastikan view v_siswa Anda menyertakan kolom idkelas agar relasi ini bekerja maksimal.
        return $this->belongsTo(Asrama::class, 'idkelas', 'idkelas');
    }

    public function boyong()
    {
        return $this->hasMany(Boyong::class, 'idperson', 'idperson');
    }
}