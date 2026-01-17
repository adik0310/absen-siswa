<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';
    protected $fillable = ['nama_kelas','id_guru'];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_kelas', 'id_kelas');
    }

    // relasi utama (nama apa pun boleh, pastikan konsisten)
    public function jadwal()
    {
        return $this->hasMany(JadwalMengajar::class, 'id_kelas', 'id_kelas');
    }

    // alias supaya kode lama yang memakai jadwalMengajar tetap bekerja
    public function jadwalMengajar()
    {
        return $this->jadwal();
    }
    // Tambahkan juga relasi ke Guru supaya nanti mudah memanggil nama walinya
public function waliKelas()
{
    return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
}
}
