<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataPelajaran extends Model
{
    use SoftDeletes;

    protected $table = 'mata_pelajaran';
    protected $primaryKey = 'id_mata_pelajaran';

    protected $fillable = ['nama_mapel'];

    public function jadwal()
    {
        return $this->hasMany(
            JadwalMengajar::class,
            'id_mapel',             // FK di jadwal_mengajar
            'id_mata_pelajaran'     // PK di sini
        );
    }
}
