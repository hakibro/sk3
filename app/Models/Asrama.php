<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asrama extends Model
{
    protected $table = 'v_asrama';
    protected $primaryKey = 'idkelas';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'idkelas',
        'asrama',
        'kamar',
        'keterangan',
    ];

    /**
     * Relasi ke Siswa
     * Satu asrama (idkelas) memiliki banyak siswa
     */
    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'idkelas', 'idkelas');
    }
}