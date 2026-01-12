<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        DB::table('roles')->insert([
            ['nama' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'guru',  'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
