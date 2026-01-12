<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_siswa',
        'nis',
        'jenis_kelamin',
        'id_kelas',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}
