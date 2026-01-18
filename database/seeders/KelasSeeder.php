<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $kelasList = [
            'X-A',
            'X-B',
            'X-C',
            'X-D',
        ];

        foreach ($kelasList as $namaKelas) {
            DB::table('kelas')->updateOrInsert(
                ['nama_kelas' => $namaKelas],
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
