<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalMengajar extends Model
{
    use SoftDeletes;

    protected $table = 'jadwal_mengajar';
    protected $primaryKey = 'id_jadwal_mengajar';

    protected $fillable = [
        'id_kelas',
        'id_mapel',
        'id_guru',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(
            MataPelajaran::class,
            'id_mapel',              // FK di jadwal_mengajar
            'id_mata_pelajaran'      // PK di mata_pelajaran
        );
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function absensi()
    {
        return $this->hasMany(
            Absensi::class,
            'id_jadwal_mengajar',
            'id_jadwal_mengajar'
        );
    }
}
