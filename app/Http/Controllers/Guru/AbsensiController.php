<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\MonthlyRekapAbsensi;
use Illuminate\Http\Request;
use App\Models\JadwalMengajar;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Exports\RekapAbsensiExport;
use Illuminate\Support\Collection;

class AbsensiController extends Controller
{

    private function isWithinSchedule($jadwal)
    {
        // Pastikan menggunakan timezone Jakarta agar sinkron dengan jam lokal
        $now = Carbon::now('Asia/Jakarta');

        // 1. Cek Hari (Pembersihan string lebih ketat)
        $todayName = strtolower(trim($now->locale('id')->isoFormat('dddd'))); 
        $dbDay = strtolower(trim(str_replace(["'", "’"], '', $jadwal->hari)));

        if ($dbDay !== $todayName) {
            return false;
        }

        // 2. Cek Jam (Gunakan format H:i:s)
        $currentTime = $now->format('H:i:s');
        $start = Carbon::parse($jadwal->jam_mulai)->format('H:i:s');
        $end   = Carbon::parse($jadwal->jam_selesai)->format('H:i:s');

        return ($currentTime >= $start && $currentTime <= $end);
    }
    /**
     * Tampilkan daftar absensi untuk sebuah jadwal (halaman input atau ringkasan hari ini).
     */
    public function index($id_jadwal_mengajar)
    {
        $jadwal = JadwalMengajar::with(['mataPelajaran', 'kelas', 'guru'])
            ->findOrFail($id_jadwal_mengajar);

        $today = Carbon::today()->toDateString();

        $absensis = Absensi::with('siswa')
            ->where('id_jadwal_mengajar', $id_jadwal_mengajar)
            ->whereDate('tanggal', $today)
            ->orderBy('id_absensi')
            ->get();

        return view('absensi.index', compact('jadwal', 'absensis'));
    }

    /**
     * Tampilkan form input absensi (create).
     */
    public function create($id_jadwal_mengajar)
{
    $jadwal = JadwalMengajar::with(['mataPelajaran', 'kelas', 'guru'])
        ->findOrFail($id_jadwal_mengajar);

    $user = Auth::user();
    $guru = $user ? $user->guru : null;

    if ($guru && $jadwal->id_guru != $guru->id_guru) {
        return redirect()->route('guru.jadwal.index')
            ->with('error', 'Anda tidak berwenang.');
    }

    // --- PERBAIKAN LOGIKA HARI ---
    $now = Carbon::now('Asia/Jakarta');
    
    // Ambil nama hari ini dalam bahasa Indonesia, kecilkan semua huruf, hapus spasi
    $todayName = strtolower(trim($now->locale('id')->isoFormat('dddd'))); 
    
    // Ambil hari dari database, kecilkan, hapus spasi, hapus tanda petik
    $jadwalHari = strtolower(trim(str_replace(["'", "’"], '', $jadwal->hari)));

    // Jika di database tertulis "Senin", $jadwalHari jadi "senin"
    // Jika hari ini Senin, $todayName jadi "senin"
    $isToday = ($todayName === $jadwalHari);

    // --- PERBAIKAN LOGIKA JAM ---
    $currentTime = $now->format('H:i:s');
    // Pastikan format jam dari database H:i:s sebelum dibandingkan
    $start = Carbon::parse($jadwal->jam_mulai)->format('H:i:s');
    $end   = Carbon::parse($jadwal->jam_selesai)->format('H:i:s');
    
    $isWithinTime = ($currentTime >= $start && $currentTime <= $end);

    // DEBUG: Kalau masih mati, coba uncomment baris di bawah ini untuk cek nilainya
    // dd($todayName, $jadwalHari, $currentTime, $start, $end);

    $siswas = Siswa::where('id_kelas', $jadwal->id_kelas)
        ->orderBy('nama_siswa')
        ->get();

    return view('absensi.create', compact('jadwal', 'siswas', 'isToday', 'isWithinTime'));
}


    /**
     * Simpan absensi (batch atau single).
     */
    public function store(Request $request, $id_jadwal_mengajar)
    {
        $jadwal = JadwalMengajar::findOrFail($id_jadwal_mengajar);

        // Validasi Waktu: Jika tidak sesuai, stop proses simpan
        if (!$this->isWithinSchedule($jadwal)) {
            return redirect()->route('guru.absensi.index', $id_jadwal_mengajar)
                ->with('error', 'Gagal Simpan: Anda hanya bisa mengisi absensi pada hari dan jam pelajaran berlangsung.');
        }

        $user = Auth::user();
        $guru = $user ? $user->guru : null;

        if ($guru && $jadwal->id_guru != $guru->id_guru) {
            return redirect()->route('guru.jadwal.index')
                ->with('error', 'Anda tidak berwenang.');
        }

        // Mode Simpan Banyak (Batch)
        if ($request->has('siswa_ids')) {
            $request->validate([
                'siswa_ids' => 'required|array|min:1',
                'keterangan' => 'required|array',
            ]);

            $siswaIds = $request->input('siswa_ids');
            $keteranganArr = $request->input('keterangan');
            $today = Carbon::today('Asia/Jakarta')->toDateString();

            $rows = [];
            foreach ($siswaIds as $idSiswa) {
                $ket = $keteranganArr[$idSiswa] ?? null;
                if (!in_array($ket, ['hadir', 'izin', 'sakit', 'alfa'])) continue;

                $rows[] = [
                    'id_jadwal_mengajar' => $id_jadwal_mengajar,
                    'id_siswa' => $idSiswa,
                    'tanggal' => $today,
                    'keterangan' => $ket,
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta'),
                ];
            }

            if (empty($rows)) {
                return redirect()->back()->with('error', 'Tidak ada data absensi yang dipilih.');
            }

            try {
                // Menggunakan upsert agar jika guru klik simpan 2x, data yang sudah ada terupdate (bukan duplikat)
                DB::table('absensi')->upsert(
                    $rows,
                    ['id_jadwal_mengajar', 'id_siswa', 'tanggal'],
                    ['keterangan', 'updated_at']
                );

                return redirect()->route('guru.absensi.index', $id_jadwal_mengajar)
                    ->with('success', count($rows) . ' data absensi berhasil disimpan.');
            } catch (\Exception $e) {
                Log::error('Absensi Save Failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menyimpan.');
            }
        }

        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    /**
     * Edit satu absensi.
     */
    public function edit($id)
    {
        $absensi = Absensi::with('siswa', 'jadwal')->where('id_absensi', $id)->firstOrFail();

        $user = Auth::user();
        $guru = $user ? $user->guru : null;
        if ($guru && $absensi->jadwal && $absensi->jadwal->id_guru && (string)$absensi->jadwal->id_guru !== (string)$guru->id_guru) {
            return redirect()->route('guru.jadwal.index')->with('error', 'Anda tidak berwenang mengedit data ini.');
        }

        return view('absensi.edit', compact('absensi'));
    }

    /**
     * Update satu absensi.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|in:hadir,izin,sakit,alfa',
        ]);

        $absensi = Absensi::where('id_absensi', $id)->firstOrFail();

        $user = Auth::user();
        $guru = $user ? $user->guru : null;
        if ($guru && $absensi->jadwal && $absensi->jadwal->id_guru && (string)$absensi->jadwal->id_guru !== (string)$guru->id_guru) {
            return redirect()->route('guru.jadwal.index')->with('error', 'Anda tidak berwenang memperbarui data ini.');
        }

        $absensi->update([
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('guru.absensi.index', ['id_jadwal_mengajar' => $absensi->id_jadwal_mengajar])
                         ->with('success', 'Absensi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $absensi = Absensi::where('id_absensi', $id)->firstOrFail();
        $jadwalId = $absensi->id_jadwal_mengajar;

        $user = Auth::user();
        $guru = $user ? $user->guru : null;
        if ($guru && $absensi->jadwal && $absensi->jadwal->id_guru && (string)$absensi->jadwal->id_guru !== (string)$guru->id_guru) {
            return redirect()->route('guru.jadwal.index')->with('error', 'Anda tidak berwenang menghapus data ini.');
        }

        $absensi->delete();

        return redirect()->route('guru.absensi.index', ['id_jadwal_mengajar' => $jadwalId])
                         ->with('success', 'Absensi berhasil dihapus.');
    }

    /**
     * Buat atau perbarui materialized monthly rekap.
     */
    public function generateMonthlyRekap($id_kelas, $year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        DB::table('monthly_rekap_absensi')
        ->where('id_kelas', $id_kelas)
        ->where('year', $year)
        ->where('month', $month)
        ->delete();

        $kelas = Kelas::with('siswa')->findOrFail($id_kelas);
        $siswaIds = $kelas->siswa->pluck('id_siswa')->toArray();

        if (empty($siswaIds)) {
            return redirect()->back()->with('error', 'Tidak ada siswa di kelas ini.');
        }

        // Ambil hitungan absensi dari tabel utama
        $absensiRows = Absensi::select(['id_siswa', 'keterangan', DB::raw('COUNT(*) as jumlah')])
            ->whereIn('id_siswa', $siswaIds)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->groupBy('id_siswa', 'keterangan')
            ->get();

        $upsertRows = [];
        foreach ($kelas->siswa as $s) {
            $siswaData = $absensiRows->where('id_siswa', $s->id_siswa);
            $hadir = $siswaData->where('keterangan', 'hadir')->first()->jumlah ?? 0;
            $sakit = $siswaData->where('keterangan', 'sakit')->first()->jumlah ?? 0;
            $izin  = $siswaData->where('keterangan', 'izin')->first()->jumlah ?? 0;
            $alfa  = $siswaData->where('keterangan', 'alfa')->first()->jumlah ?? 0;

            $upsertRows[] = [
                'id_kelas' => $id_kelas,
                'id_siswa' => $s->id_siswa,
                'year' => (int)$year,
                'month' => (int)$month,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alfa' => $alfa,
                'total' => $hadir + $sakit + $izin + $alfa,
                'nama_siswa' => $s->nama_siswa,
                'nis' => $s->nis,
                'nama_kelas' => $kelas->nama_kelas,
                'updated_at' => now(),
                'created_at' => now(),
            ];
        }

        try {
            DB::table('monthly_rekap_absensi')->upsert(
                $upsertRows,
                ['id_kelas', 'id_siswa', 'year', 'month'],
                ['hadir', 'sakit', 'izin', 'alfa', 'total', 'updated_at']
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update rekap.');
        }

        // KEMBALI KE HALAMAN REKAP DENGAN PARAMETER LENGKAP
        return redirect()->route('guru.absensi.rekap', [
            'id_kelas' => $id_kelas,
            'year' => $year,
            'month' => $month,
            'kelas_id' => $id_kelas
        ])->with('success', 'Data berhasil diperbarui!');
    }

    public function viewMonthlyRekap($id_kelas = null, $year = null, $month = null)
{
    $allKelas = Kelas::orderBy('nama_kelas')->get();
    
    // Kita cek apakah ada input 'kelas_id' dari form GET
    $selKelasId = request()->query('kelas_id'); 
    $year = $year ?? now()->year;
    $month = $month ?? now()->month;

    // JIKA tidak ada parameter kelas_id di URL, langsung tampilkan view kosong
    if (!$selKelasId) {
        return view('absensi.rekap_materialized', [
            'rekap' => null, 
            'allKelas' => $allKelas, 
            'year' => $year, 
            'month' => $month,
            'kelas' => null, 
            'mapels' => [], 
            'selKelas' => null, // Tambahkan ini agar Blade mengenali variabel $selKelas
            'selMapel' => null,
            'currentMapelName' => null
        ]);
    }

    // JIKA ADA kelas_id, baru kita proses datanya
    $kelas = Kelas::with('siswa')->findOrFail($selKelasId);
    $mapelId = request()->query('mapel_id');
    
    // Ambil daftar mapel untuk dropdown filter
    $mapelIds = JadwalMengajar::where('id_kelas', $selKelasId)
                ->pluck('id_mapel')
                ->unique()
                ->filter()
                ->values()
                ->all();
                
    $mapels = MataPelajaran::whereIn('id_mata_pelajaran', $mapelIds)
              ->orderBy('nama_mapel')
              ->get();

    $currentMapelName = $mapelId ? optional(MataPelajaran::find($mapelId))->nama_mapel : null;

    $data = $this->collectRekapWithFilters($selKelasId, $year, $month, $mapelId);
    $rekap = $data['rekap']->unique('id_siswa');

    return view('absensi.rekap_materialized', [
        'kelas' => $kelas,
        'rekap' => $rekap,
        'year' => $year,
        'month' => $month,
        'mapels' => $mapels,
        'mapelId' => $mapelId,
        'selKelas' => $selKelasId, // Pastikan dikirim ke view
        'selMapel' => $mapelId,    // Pastikan dikirim ke view
        'currentMapelName' => $currentMapelName,
        'allKelas' => $allKelas
    ]);
}
    /**
     * Export rekap (kelas) ke PDF (DomPDF).
     */
    public function exportMonthlyRekapPdf(Request $request, $id_kelas, $year = null, $month = null)
    {
        $year  = (int) ($year ?? now()->year);
        $month = (int) ($month ?? now()->month);
        $mapelId = $request->query('mapel_id');

        $kelas = Kelas::with('siswa')->findOrFail($id_kelas);
        
        // 1. AMBIL DATA GURU YANG LOGIN
        $user = Auth::user();
        $namaGuruLogin = $user->guru ? $user->guru->nama_guru : $user->name;
        $nipGuru = $user->guru ? $user->guru->nip : '..........................';

        // 2. AMBIL NAMA MAPEL
        $currentMapelName = null;
        if ($mapelId) {
            $mapel = MataPelajaran::find($mapelId);
            $currentMapelName = $mapel ? $mapel->nama_mapel : null;
        }

        // 3. AMBIL DATA REKAP
        $data = $this->collectRekapWithFilters($id_kelas, $year, $month, $mapelId);
        $rekap = $data['rekap'];

        // 4. AMBIL DATA HARIAN (TANGGAL 1-31)
        foreach ($rekap as $r) {
            $absensiHarian = Absensi::where('id_siswa', $r->id_siswa)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->when($mapelId, function($q) use ($mapelId) {
                    return $q->whereHas('jadwal', fn($jq) => $jq->where('id_mapel', $mapelId));
                })
                ->get();

            $harian = [];
            foreach ($absensiHarian as $a) {
                $harian[$a->tanggal] = $a->keterangan;
            }
            $r->harian = $harian;
        }

        // 5. GENERATE PDF
        $pdf = Pdf::loadView('absensi.rekap_pdf', [
            'kelas' => $kelas,
            'rekap' => $rekap,
            'year'  => $year,
            'month' => $month,
            'currentMapelName' => $currentMapelName,
            'namaGuruLogin' => $namaGuruLogin, // Dikirim ke view
            'nipGuru' => $nipGuru             // Dikirim ke view
        ])->setPaper('a4', 'landscape'); // WAJIB LANDSCAPE

        return $pdf->download("rekap_absen_{$kelas->nama_kelas}_{$month}_{$year}.pdf");
    }
    /**
     * Export rekap (kelas) ke Excel.
     */
    public function exportMonthlyRekapExcel(Request $request, $id_kelas, $year = null, $month = null)
{
    $year = (int) ($year ?? now()->year);
    $month = (int) ($month ?? now()->month);
    $mapelId = request()->query('mapel_id');

    // 1. Ambil Data Guru Login (Sama seperti PDF)
    $user = Auth::user();
    $namaGuruLogin = $user->guru ? $user->guru->nama_guru : $user->name;
    $nipGuru = $user->guru ? $user->guru->nip : '..........................';

    $jadwal = JadwalMengajar::with(['kelas', 'mataPelajaran', 'guru'])
                ->where('id_kelas', $id_kelas)
                ->where('id_mapel', $request->mapel_id)
                ->first();

    // 2. Ambil Data Dasar
    $dataRaw = $this->collectRekapWithFilters($id_kelas, $year, $month, $mapelId);
    $rekap = $dataRaw['rekap'];

    // 3. Ambil Detail Harian (Sekaligus agar cepat)
    $allAbsensi = Absensi::whereIn('id_siswa', $rekap->pluck('id_siswa'))
        ->whereYear('tanggal', $year)
        ->whereMonth('tanggal', $month)
        ->when($mapelId, function($q) use ($mapelId) {
            return $q->whereHas('jadwal', fn($jq) => $jq->where('id_mapel', $mapelId));
        })
        ->get()
        ->groupBy('id_siswa');

    foreach ($rekap as $r) {
        $r->harian = $allAbsensi->get($r->id_siswa, collect())
                                ->pluck('keterangan', 'tanggal')
                                ->toArray();
    }

    // 4. Kirim Payload
    $payload = [
        'rekap' => $rekap,
        'year'  => $year,
        'month' => $month,
        'kelas' => $dataRaw['kelas'],
        'currentMapelName' => $mapelId ? optional(MataPelajaran::find($mapelId))->nama_mapel : 'Semua Mata Pelajaran',
        'namaGuruLogin' => $namaGuruLogin,
        'nipGuru' => $nipGuru,
        'jadwal' => $jadwal,
    ];

    $fileName = "rekap_absensi_" . str_replace(' ', '_', $dataRaw['kelas']->nama_kelas) . "_{$year}_{$month}.xlsx";
    
    return Excel::download(new \App\Exports\RekapAbsensiExport($payload), $fileName);
}

    /**
     * View rekap berdasarkan 1 jadwal.
     */
    public function viewRekapByJadwal($id_jadwal, $year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $jadwal = JadwalMengajar::with(['mataPelajaran', 'kelas', 'guru'])->findOrFail($id_jadwal);

        $user = Auth::user();
        $currentGuru = optional($user->guru);
        if ($currentGuru->id_guru ?? null) {
            if ($jadwal->id_guru && (string)$jadwal->id_guru !== (string)$currentGuru->id_guru) {
                return redirect()->back()->with('error', 'Anda tidak berwenang melihat rekap untuk jadwal ini.');
            }
        }

        $data = $this->collectRekapForJadwal($id_jadwal, $year, $month);

        return view('absensi.rekap_by_jadwal', [
            'jadwal' => $jadwal,
            'rekap' => $data['rekap'],
            'year' => $year,
            'month' => $month,
        ]);
    }

public function exportPdfByJadwal($id_jadwal, $year, $month)
{
    $year = (int)$year;
    $month = (int)$month;
    
    $jadwal = JadwalMengajar::with(['kelas', 'mataPelajaran', 'guru'])->findOrFail($id_jadwal);
    
    $user = Auth::user();
    $namaGuruLogin = $user->guru ? $user->guru->nama_guru : $user->name;
    $nipGuru = $user->guru ? $user->guru->nip : '..........................';

    // --- TAMBAHKAN BARIS INI ---
    // Menggunakan Carbon untuk mendapatkan tanggal hari ini dalam format Indonesia
    $tanggalCetak = \Carbon\Carbon::now()->translatedFormat('d F Y');
    // ---------------------------

    // Ambil semua siswa di kelas tersebut
    $siswas = Siswa::where('id_kelas', $jadwal->id_kelas)->orderBy('nama_siswa')->get();

    // Ambil data absensi detail untuk bulan tersebut spesifik id_jadwal ini
    $allAbsensi = Absensi::where('id_jadwal_mengajar', $id_jadwal)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->get()
                ->groupBy('id_siswa');

    $rekap = $siswas->map(function($s) use ($allAbsensi) {
        $logs = $allAbsensi->get($s->id_siswa, collect());
        
        $harian = [];
        foreach($logs as $log) {
            $harian[$log->tanggal] = $log->keterangan;
        }

        return (object)[
            'id_siswa'   => $s->id_siswa,
            'nama_siswa' => $s->nama_siswa,
            'nis'        => $s->nis,
            'harian'     => $harian,
            'hadir'      => $logs->where('keterangan', 'hadir')->count(),
            'sakit'      => $logs->where('keterangan', 'sakit')->count(),
            'izin'       => $logs->where('keterangan', 'izin')->count(),
            'alfa'       => $logs->where('keterangan', 'alfa')->count(),
        ];
    });

    $pdf = Pdf::loadView('absensi.rekap_by_jadwal_pdf', [
        'jadwal' => $jadwal,
        'rekap'  => $rekap,
        'year'   => $year,
        'month'  => $month,
        'namaGuruLogin' => $namaGuruLogin,
        'nipGuru' => $nipGuru,
        'tanggalCetak' => $tanggalCetak // <-- Pastikan ini dikirim ke view
    ])->setPaper('a4', 'landscape');

    return $pdf->download("rekap_harian_jadwal_{$jadwal->mataPelajaran->nama_mapel}.pdf");
}

public function exportExcelByJadwal($id_jadwal, $year, $month)
{
    $year = (int)$year;
    $month = (int)$month;
    
    $jadwal = JadwalMengajar::with(['kelas', 'mataPelajaran', 'guru'])->findOrFail($id_jadwal);
    $user = Auth::user();

    // 1. Ambil semua siswa di kelas tersebut
    $siswas = Siswa::where('id_kelas', $jadwal->id_kelas)->orderBy('nama_siswa')->get();

    // 2. Ambil data absensi detail (Harian)
    $allAbsensi = Absensi::where('id_jadwal_mengajar', $id_jadwal)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->get()
                ->groupBy('id_siswa');

    // 3. Olah data agar punya property 'harian'
    $rekap = $siswas->map(function($s) use ($allAbsensi) {
        $logs = $allAbsensi->get($s->id_siswa, collect());
        
        $harian = [];
        foreach($logs as $log) {
            // Kita simpan tanggal sebagai key, dan keterangan sebagai value
            $harian[$log->tanggal] = $log->keterangan;
        }

        return (object)[
            'id_siswa'   => $s->id_siswa,
            'nama_siswa' => $s->nama_siswa,
            'nis'        => $s->nis,
            'harian'     => $harian,
            'hadir'      => $logs->where('keterangan', 'hadir')->count(),
            'sakit'      => $logs->where('keterangan', 'sakit')->count(),
            'izin'       => $logs->where('keterangan', 'izin')->count(),
            'alfa'       => $logs->where('keterangan', 'alfa')->count(),
        ];
    });

    $payload = [
        'jadwal' => $jadwal,
        'rekap' => $rekap, // Sekarang rekap sudah punya data harian
        'year' => $year,
        'month' => $month,
        'namaGuruLogin' => $user->guru ? $user->guru->nama_guru : $user->name,
        'nipGuru' => $user->guru ? $user->guru->nip : '-'
    ];

    $fileName = "rekap_excel_{$jadwal->mataPelajaran->nama_mapel}_{$year}_{$month}.xlsx";
    return Excel::download(new \App\Exports\RekapJadwalExport($payload), $fileName);
}

    /**
     * Helper: Kumpulkan rekap untuk kelas $id_kelas sesuai possible filters mapelId/guruId.
     */
    // Helper Filter (Sama seperti sebelumnya)
    protected function collectRekapWithFilters($id_kelas, $year, $month, $mapelId = null, $guruId = null)
    {
        $kelas = Kelas::with('siswa')->findOrFail($id_kelas);
        $siswaIds = $kelas->siswa->pluck('id_siswa')->toArray();

        // Jika TANPA filter Mapel, ambil dari tabel Materialized (Lebih Cepat)
        if (!$mapelId && !$guruId) {
            $rekap = MonthlyRekapAbsensi::where('id_kelas', $id_kelas)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->orderBy('nama_siswa')
                        ->get();
            
            // Jika tabel materialized kosong, kembalikan koleksi kosong agar tidak error
            return ['kelas' => $kelas, 'rekap' => $rekap];
        }

        // Jika ADA filter Mapel, hitung manual dari tabel Absensi Utama
        $absensiQuery = Absensi::whereIn('id_siswa', $siswaIds)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month);

        if ($mapelId) {
            $absensiQuery->whereHas('jadwal', fn($q) => $q->where('id_mapel', $mapelId));
        }

        $filteredAbsensi = $absensiQuery->get();
        $rekap = $kelas->siswa->map(function($s) use ($filteredAbsensi) {
            $mine = $filteredAbsensi->where('id_siswa', $s->id_siswa);
            return (object)[
                'id_siswa'   => $s->id_siswa,
                'nama_siswa' => $s->nama_siswa,
                'nis'        => $s->nis,
                'hadir'      => $mine->where('keterangan', 'hadir')->count(),
                'izin'       => $mine->where('keterangan', 'izin')->count(),
                'sakit'      => $mine->where('keterangan', 'sakit')->count(),
                'alfa'       => $mine->where('keterangan', 'alfa')->count(),
                'total'      => $mine->count(),
            ];
        })->sortBy('nama_siswa');

        return ['kelas' => $kelas, 'rekap' => $rekap];
    }

    /**
     * Helper: kumpulkan rekap berdasarkan id_jadwal_mengajar
     */
    protected function collectRekapForJadwal($id_jadwal, $year, $month)
    {
        $jadwal = JadwalMengajar::with('kelas','mataPelajaran','guru')->findOrFail($id_jadwal);
        $kelas = Kelas::with('siswa')->findOrFail($jadwal->id_kelas);
        $siswaIds = $kelas->siswa->pluck('id_siswa')->toArray();

        if (empty($siswaIds)) {
            return ['rekap' => collect()];
        }

        $absensi = Absensi::where('id_jadwal_mengajar', $id_jadwal)
                    ->whereIn('id_siswa', $siswaIds)
                    ->whereYear('tanggal', $year)
                    ->whereMonth('tanggal', $month)
                    ->get();

        $rekapMap = [];
        foreach ($kelas->siswa as $s) {
            $rekapMap[$s->id_siswa] = (object)[
                'id_siswa' => $s->id_siswa,
                'nama_siswa' => $s->nama_siswa,
                'nis' => $s->nis ?? null,
                'hadir' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alfa' => 0,
                'total' => 0,
            ];
        }

        foreach ($absensi as $r) {
            if (!isset($rekapMap[$r->id_siswa])) continue;
            $ket = $r->keterangan;
            if ($ket === 'hadir') $rekapMap[$r->id_siswa]->hadir++;
            if ($ket === 'izin') $rekapMap[$r->id_siswa]->izin++;
            if ($ket === 'sakit') $rekapMap[$r->id_siswa]->sakit++;
            if ($ket === 'alfa') $rekapMap[$r->id_siswa]->alfa++;
            $rekapMap[$r->id_siswa]->total++;
        }

        $rekap = collect(array_values($rekapMap))->sortBy('nama_siswa')->values();

        return ['rekap' => $rekap];
    }
    
}