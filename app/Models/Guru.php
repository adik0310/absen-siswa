<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guru extends Model
{
    use SoftDeletes;

    protected $table = 'guru';
    protected $primaryKey = 'id_guru';
    protected $fillable = ['nama_guru', 'nip', 'id_users'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_users', 'id_users');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalMengajar::class, 'id_guru', 'id_guru');
    }
}
