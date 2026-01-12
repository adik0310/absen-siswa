<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $jadwalTable = 'jadwal_mengajar';
        $siswaTable  = 'siswa';
        $absensiTable= 'absensi';

        // ambil semua jadwal yang tersedia
        $jadwals = DB::table($jadwalTable)
            ->select('id_jadwal_mengajar', 'id_kelas')
            ->get()
            ->toArray();

        // ambil semua siswa yang tersedia, group by kelas
        $siswaRows = DB::table($siswaTable)
            ->select('id_siswa', 'id_kelas')
            ->get()
            ->groupBy('id_kelas');

        if (empty($jadwals)) {
            $this->command->info("AbsensiSeeder aborted: tidak ditemukan jadwal_mengajar.");
            return;
        }
        if (DB::table($siswaTable)->count() === 0) {
            $this->command->info("AbsensiSeeder aborted: tidak ditemukan siswa.");
            return;
        }

        // contoh tanggal
        $dates = [
            Carbon::parse('2025-12-01'),
            Carbon::parse('2025-12-02'),
            Carbon::parse('2025-12-03'),
        ];

        $rows = [];

        foreach ($jadwals as $jadwal) {
            $kelasId = $jadwal->id_kelas;
            $availableSiswa = $siswaRows[$kelasId] ?? null;

            if ($availableSiswa && count($availableSiswa) > 0) {
                $sIds = array_slice($availableSiswa->pluck('id_siswa')->toArray(), 0, 5);
            } else {
                // fallback ke siswa acak jika tidak ada siswa di kelas tersebut
                $sIds = DB::table($siswaTable)->inRandomOrder()->limit(5)->pluck('id_siswa')->toArray();
                if (empty($sIds)) continue;
            }

            foreach ($sIds as $sId) {
                foreach ($dates as $d) {
                    $kList = ['hadir','sakit','izin','alfa'];
                    $k = $kList[($sId + $jadwal->id_jadwal_mengajar + $d->day) % count($kList)];

                    $rows[] = [
                        'id_jadwal_mengajar' => $jadwal->id_jadwal_mengajar,
                        'id_siswa' => $sId,
                        'tanggal' => $d->toDateString(),
                        'keterangan' => $k,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (!empty($rows)) {
            DB::table($absensiTable)->insert($rows);
            $this->command->info('AbsensiSeeder: inserted ' . count($rows) . ' rows.');
        } else {
            $this->command->info('AbsensiSeeder: nothing to insert (no valid mapping).');
        }
    }
}
