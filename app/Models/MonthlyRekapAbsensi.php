<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlyRekapAbsensi extends Model
{
    use SoftDeletes;

    protected $table = 'monthly_rekap_absensi';
    protected $fillable = [
        'id_kelas','id_siswa','year','month',
        'hadir','sakit','izin','alfa','total',
        'nama_siswa','nis','nama_kelas'
    ];
    public $timestamps = true;
}
