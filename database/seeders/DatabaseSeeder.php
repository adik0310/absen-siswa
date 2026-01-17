<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // urutan penting: roles -> permissions -> users -> kelas -> mata_pelajaran -> guru -> siswa -> jadwal -> absensi -> rekap
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
