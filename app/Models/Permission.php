<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    protected $table = 'permissions';
    protected $primaryKey = 'id_permissions';
    protected $fillable = ['nama'];

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_permissions',
            'id_permission',
            'id_role'
        )->withTimestamps();
    }
}
