<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Absensi extends Model
{
    use SoftDeletes;

    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';

    protected $fillable = [
        'id_jadwal_mengajar',
        'id_siswa',
        'tanggal',
        'jam_masuk',
        'jam_keluar',   
        'keterangan',
    ];

    public function jadwal()
    {
        return $this->belongsTo(
            JadwalMengajar::class,
            'id_jadwal_mengajar',
            'id_jadwal_mengajar'
        );
    }

    public function siswa()
    {
        return $this->belongsTo(
            Siswa::class,
            'id_siswa',
            'id_siswa'
        );
    }
}
