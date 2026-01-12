<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapAbsensi extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_absensi';
    protected $primaryKey = 'id_rekap_absensi';
    protected $fillable = ['id_siswa', 'id_absensi', 'nama', 'nis', 'kelas', 'keterangan'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'id_absensi', 'id_absensi');
    }
}
