<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_users';

    protected $fillable = [
        'nama',
        'email',
        'id_role',
        'password',
        'foto',
    ];

    // --- TAMBAHKAN KODE INI ---
    /**
     * Relasi ke model Guru
     */
    public function guru()
    {
        // 'id_users' adalah PK di tabel users
        // 'id_users' (parameter kedua) adalah FK di tabel gurus
        return $this->hasOne(Guru::class, 'id_users', 'id_users');
    }
    // --------------------------

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}