<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function kopSurat(): array
    {
        return [
            'nama_pesantren' => static::getValue('kop_nama_pesantren', 'PONDOK PESANTREN NGALAH'),
            'nspp' => static::getValue('kop_nspp', '510035140166'),
            'alamat' => static::getValue('kop_alamat', 'Jl. Pesantren Ngalah No. 16 Sengonagung Purwosari Pasuruan 67162'),
        ];
    }

    /**
     * Batas tanggal (hari dalam bulan) untuk aturan cut-off SPP bulan berjalan.
     * Default 6: bila tanggal boyong > batas, SPP bulan berjalan tetap full.
     */
    public static function cutOffTanggalMax(): int
    {
        return (int) (static::getValue('cut_off_tanggal_max', '6') ?? 6);
    }
}
