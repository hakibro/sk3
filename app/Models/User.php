<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'lembaga'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah Pengurus Pusat
     */
    public function isPusat(): bool
    {
        return $this->role === 'pengurus_pusat';
    }

    /**
     * Cek apakah user adalah Pengurus Asrama
     */
    public function isAsrama(): bool
    {
        return $this->role === 'pengurus_asrama';
    }

    /**
     * Relasi ke pengajuan boyong yang dibuat oleh user ini
     */
    public function daftarAjukanBoyong()
    {
        return $this->hasMany(Boyong::class, 'user_id');
    }

    public function daftarValidasiBoyong()
    {
        return $this->hasMany(Boyong::class, 'approved_by');
    }
}
