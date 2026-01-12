<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\JadwalMengajar;
use App\Models\Absensi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        // -------------------------------------
        // 1. DATA DASAR
        // -------------------------------------
        $jumlah_siswa  = (int) Siswa::count();
        $jumlah_guru   = (int) Guru::count();
        $jumlah_kelas  = (int) Kelas::count();
        $jumlah_mapel  = (int) MataPelajaran::count();
        $total_absensi = (int) Absensi::count();


        // -------------------------------------
        // 2. DETAIL PRESENSI HARI INI
        // -------------------------------------
        $absensiHariIni = Absensi::whereDate('tanggal', $today)
            ->with(['jadwal.guru', 'jadwal.kelas', 'jadwal.mataPelajaran'])
            ->get()
            ->groupBy('id_jadwal_mengajar');

        $detail_presensi = [];

        $total_hadir_semua_jadwal = 0;
        $total_siswa_semua_jadwal = 0;

        foreach ($absensiHariIni as $idJadwal => $logs) {

            $jadwal = $logs->first()->jadwal;

            if ($jadwal) {

                $totalSiswaKelas = Siswa::where('id_kelas', $jadwal->id_kelas)->count();

                // HANYA yang hadir
                $sudahAbsen = $logs->where('keterangan', 'hadir')
                                   ->unique('id_siswa')
                                   ->count();

                // Simpan totalnya untuk CARD atas
                $total_hadir_semua_jadwal += $sudahAbsen;
                $total_siswa_semua_jadwal += $totalSiswaKelas;

                // Progress = dari total siswa kelas (tidak dikurangi)
                $totalProgress = $totalSiswaKelas;

                $detail_presensi[] = [
                    'kelas'       => $jadwal->kelas->nama_kelas ?? '-',
                    'mapel'       => $jadwal->mataPelajaran->nama_mapel ?? '-',
                    'guru'        => $jadwal->guru->nama_guru ?? '-',
                    'total_siswa' => $totalSiswaKelas,
                    'sudah_absen' => $sudahAbsen,
                    'total_progress' => $totalProgress,
                ];
            }
        }

        // -------------------------------------
        // 3. CARD ATAS: BELUM ABSEN (versi baru)
        // -------------------------------------
        $sudah_presensi = $total_hadir_semua_jadwal;

        // TOTAL siswa = dijumlah dari setiap jadwal (sesuai permintaanmu)
        $belum_presensi = max(0, $total_siswa_semua_jadwal - $total_hadir_semua_jadwal);


        // -------------------------------------
        // 4. KETIDAKHADIRAN (Sakit, Izin, Alfa)
        // -------------------------------------
        $ketidakhadiranQuery = Absensi::whereDate('tanggal', $today)
            ->selectRaw("
                SUM(CASE WHEN keterangan = 'sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN keterangan = 'izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN keterangan = 'alfa' THEN 1 ELSE 0 END) as alfa
            ")->first();

        $ketidakhadiran = [
            'sakit' => (int) ($ketidakhadiranQuery->sakit ?? 0),
            'izin'  => (int) ($ketidakhadiranQuery->izin ?? 0),
            'alfa'  => (int) ($ketidakhadiranQuery->alfa ?? 0),
        ];


        // -------------------------------------
        // 5. TREND 5 HARI
        // -------------------------------------
        $dates = collect();
        for ($i = 4; $i >= 0; $i--) {
            $dates->push(Carbon::today()->subDays($i));
        }

        $rawTrend = Absensi::selectRaw("tanggal, COUNT(DISTINCT id_siswa) as hadir")
            ->whereBetween('tanggal', [
                $dates->first()->toDateString(),
                $dates->last()->toDateString()
            ])
            ->where('keterangan', 'hadir')
            ->groupBy('tanggal')
            ->pluck('hadir', 'tanggal')
            ->toArray();

        $trend_kehadiran = [];
        foreach ($dates as $d) {
            $trend_kehadiran[] = [
                'tanggal' => $d->format('d-m-Y'),
                'jumlah'  => (int) ($rawTrend[$d->toDateString()] ?? 0),
            ];
        }


        // -------------------------------------
        // 6. KIRIM KE VIEW
        // -------------------------------------
        $data = [
            'jumlah_siswa'    => $jumlah_siswa,
            'sudah_presensi'  => $sudah_presensi,
            'belum_presensi'  => $belum_presensi,
            'ketidakhadiran'  => $ketidakhadiran,
            'trend_kehadiran' => $trend_kehadiran,
        ];

        return view('admin.dashboard', compact(
            'data', 'detail_presensi', 'jumlah_guru',
            'jumlah_kelas', 'jumlah_mapel', 'total_absensi'
        ));
    }
}
