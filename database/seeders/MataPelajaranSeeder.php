<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $table = 'mata_pelajaran';

        $mapelList = [
            'Biologi',
            'Bahasa Sunda',
            'Fikih',
            'Penjas',
            'PPKn',
            'Matematika',
            'Kimia',
            'Fasilitator P5',
            'Bahasa Inggris',
            'Bahasa Indonesia',
            'Informatika',
            'Teknologi Informatika',
            'BTQ',
            'Seni Budaya',
            'Sejarah',
            'Geografi',
            'Fisika',
            'Ekonomi',
            'Ushul Fikih',
            'Akidah Akhlak',
            'SKI',
            'Qur\'an Hadits',
            'IPA (Biologi)',
            'Sosiologi',
        ];

        $mapelList = array_values(array_unique($mapelList));

        $now = now();
        $data = [];
        foreach ($mapelList as $index => $nama) {
            $data[] = [
                'nama_mapel' => $nama,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        Schema::disableForeignKeyConstraints();
        DB::table($table)->truncate();
        if (!empty($data)) {
            DB::table($table)->insert($data);
        }
        Schema::enableForeignKeyConstraints();

        echo "Seeder MataPelajaran: selesai, total masuk: " . count($data) . "\n";
    }
}