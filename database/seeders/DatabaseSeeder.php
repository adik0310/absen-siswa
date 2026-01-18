<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            KelasSeeder::class,
            GuruSeeder::class,    // kalau Anda punya
            UserSeeder::class,
            MataPelajaranSeeder::class,
            SiswaSeeder::class,
            JadwalMengajarSeeder::class,
            // RekapAbsensiSeeder::class,   // kalau ada
        ]);
    }
}
