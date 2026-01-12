<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $perms = [
            'view_absensi','create_absensi','update_absensi','delete_absensi',
            'view_siswa','create_siswa','update_siswa','delete_siswa',
            'view_guru','create_guru','update_guru','delete_guru',
            'manage_users','manage_roles','manage_permissions'
        ];

        $rows = [];
        foreach ($perms as $p) {
            $rows[] = ['nama' => $p, 'created_at' => $now, 'updated_at' => $now];
        }

        DB::table('permissions')->insert($rows);
    }
}
