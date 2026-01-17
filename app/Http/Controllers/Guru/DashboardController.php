<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\JadwalMengajar;
use App\Models\Absensi;
use App\Models\Guru;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');

        $user = Auth::user();

        // Cari relasi guru
        $guruRecord = null;
        if ($user) {
            $guruRecord = Guru::where('id_users', $user->id_users)->first();
        }

        $kelasWali = null;
        if ($guruRecord) {
            // Cari di tabel kelas yang kolom id_guru-nya adalah ID guru ini
            $kelasWali = \App\Models\Kelas::where('id_guru', $guruRecord->id_guru)->first();
        }

        // Siapkan query jadwal
        $jadwalQuery = JadwalMengajar::query();
        
        if ($guruRecord) {
            $jadwalQuery->where('id_guru', $guruRecord->id_guru);
        } elseif (Schema::hasColumn('jadwal_mengajar', 'id_users')) {
            $jadwalQuery->where('id_users', $user->id_users);
        }

        // Ambil semua jadwal milik guru
        $jadwals = $jadwalQuery->with(['mataPelajaran', 'kelas', 'guru'])->get();

        // --- Tanggal hari ini
        $today = Carbon::today()->toDateString();
        $todayName = Carbon::now()->translatedFormat('l'); // Contoh: Jumat
        $todayNameEn = Carbon::now()->format('l');         // Contoh: Friday

        // --- Ambil ID jadwal untuk hitung absensi
        $jadwalIds = $jadwals->map(fn($j) => $j->id_jadwal_mengajar ?? $j->id)->filter()->unique()->values();

        // --- Hitung hadir hari ini
        $hadirHariIni = 0;
        if ($jadwalIds->isNotEmpty()) {
            $hadirHariIni = Absensi::whereDate('tanggal', $today)
                ->whereIn('id_jadwal_mengajar', $jadwalIds)
                ->where('keterangan', 'hadir')
                ->count();
        }

        // --- Ambil data absensi hari ini (untuk tabel aktivitas)
        $absensiToday = collect();
        if ($jadwalIds->isNotEmpty()) {
            $absensiRows = Absensi::whereDate('tanggal', $today)
                ->whereIn('id_jadwal_mengajar', $jadwalIds)
                ->with('siswa')
                ->get();

            $jadwalMap = $jadwals->keyBy(fn($j) => (string)($j->id_jadwal_mengajar ?? $j->id));

            $absensiToday = $absensiRows->map(function ($a) use ($jadwalMap) {
                $jadwal = $jadwalMap->get((string)$a->id_jadwal_mengajar);
                return (object)[
                    'nama_siswa' => $a->siswa->nama_siswa ?? '-',
                    'mapel' => $jadwal->mataPelajaran->nama_mapel ?? '-',
                    'kelas' => $jadwal->kelas->nama_kelas ?? '-',
                    'keterangan' => $a->keterangan,
                ];
            });
        }

        // --- FIX LOGIKA HARI: Membersihkan tanda kutip agar "Jum'at" == "Jumat"
        $cleanToday = str_replace(["'", "’"], "", strtolower($todayName));
        $cleanTodayEn = str_replace(["'", "’"], "", strtolower($todayNameEn));
        $possibleNames = [$cleanToday, $cleanTodayEn];

        $jadwalsToday = $jadwals->filter(function ($j) use ($possibleNames) {
            $hariDb = str_replace(["'", "’"], "", strtolower((string)$j->hari));
            return in_array($hariDb, $possibleNames);
        })->unique(function ($j) {
            return $j->id_jadwal_mengajar ?? $j->id;
        })->values();

        return view('guru.dashboard', [
            'user' => $user,
            'hadirHariIni' => $hadirHariIni,
            'absensiToday' => $absensiToday,
            'jadwalsToday' => $jadwalsToday,
            'todayName' => $todayName,
            'kelasWali' => $kelasWali,
        ]);
    }
}