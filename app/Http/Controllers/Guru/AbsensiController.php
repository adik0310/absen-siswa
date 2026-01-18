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
     * Tampilkan halaman scanner QR Code.
     */
    public function scan($id_jadwal_mengajar)
    {
        $jadwal = JadwalMengajar::with(['mataPelajaran', 'kelas', 'guru'])
            ->findOrFail($id_jadwal_mengajar);

        // Validasi akses guru
        $user = Auth::user();
        $guru = $user ? $user->guru : null;
        if ($guru && $jadwal->id_guru != $guru->id_guru) {
            return redirect()->route('guru.jadwal.index')->with('error', 'Anda tidak berwenang.');
        }

        return view('absensi.scan', compact('jadwal'));
    }

    // File: app/Http/Controllers/Guru/AbsensiController.php

public function storeScan(Request $request, $id_jadwal_mengajar)
{
    $jadwal = JadwalMengajar::findOrFail($id_jadwal_mengajar);

    // 1. Cek Waktu & Hari
    if (!$this->isWithinSchedule($jadwal)) {
        return response()->json(['success' => false, 'message' => 'Scanner tidak aktif di luar jam pelajaran.'], 403);
    }

    // 2. Cari Siswa
    $siswa = Siswa::where('nis', $request->nis)->where('id_kelas', $jadwal->id_kelas)->first();
    if (!$siswa) {
        return response()->json(['success' => false, 'message' => 'Siswa tidak terdaftar di kelas ini.'], 404);
    }

    $today = Carbon::today('Asia/Jakarta')->toDateString();
    $now = Carbon::now('Asia/Jakarta');

    // 3. Cek data absensi hari ini
    $absensi = Absensi::where('id_siswa', $siswa->id_siswa)
                      ->where('id_jadwal_mengajar', $id_jadwal_mengajar)
                      ->whereDate('tanggal', $today)
                      ->first();

    try {
        // --- PROSES SCAN 1 (MASUK) ---
        if (!$absensi) {
            Absensi::create([
                'id_jadwal_mengajar' => $id_jadwal_mengajar,
                'id_siswa' => $siswa->id_siswa,
                'tanggal' => $today,
                'jam_masuk' => $now->format('H:i:s'),
                'keterangan' => 'alfa', // Belum dianggap hadir karena belum scan keluar
            ]);

            return response()->json([
                'success' => true,
                'nama' => $siswa->nama_siswa,
                'message' => 'Scan MASUK berhasil. Jam: ' . $now->format('H:i')
            ]);
        }

        // --- PROSES SCAN 2 (KELUAR) ---
        if ($absensi && is_null($absensi->jam_keluar)) {
            $absensi->update([
                'jam_keluar' => $now->format('H:i:s'),
                'keterangan' => 'hadir', // Sekarang statusnya sah HADIR
            ]);

            return response()->json([
                'success' => true,
                'nama' => $siswa->nama_siswa,
                'message' => 'Scan KELUAR berhasil. Status: HADIR'
            ]);
        }

        // Jika sudah scan masuk dan keluar
        return response()->json([
            'success' => false,
            'message' => $siswa->nama_siswa . ' sudah lengkap (Masuk & Keluar).'
        ]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Gagal mencatat absensi.'], 500);
    }
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
    $jadwal = JadwalMengajar::with(['mataPelajaran', 'kelas', 'guru'])->findOrFail($id_jadwal_mengajar);

    // Cek Akses Guru
    $user = Auth::user();
    $guru = $user ? $user->guru : null;
    if ($guru && $jadwal->id_guru != $guru->id_guru) {
        return redirect()->route('guru.jadwal.index')->with('error', 'Anda tidak berwenang.');
    }

    // Cek Waktu
    $now = Carbon::now('Asia/Jakarta');
    $todayName = strtolower(trim($now->locale('id')->isoFormat('dddd'))); 
    $jadwalHari = strtolower(trim(str_replace(["'", "’"], '', $jadwal->hari)));
    $isToday = ($todayName === $jadwalHari);

    $currentTime = $now->format('H:i:s');
    $start = Carbon::parse($jadwal->jam_mulai)->format('H:i:s');
    $end   = Carbon::parse($jadwal->jam_selesai)->format('H:i:s');
    $isWithinTime = ($currentTime >= $start && $currentTime <= $end);

    // AMBIL DATA ABSENSI HARI INI
    $today = Carbon::today('Asia/Jakarta')->toDateString();
    $alreadyAbsen = Absensi::where('id_jadwal_mengajar', $id_jadwal_mengajar)
        ->whereDate('tanggal', $today)
        ->get()
        ->keyBy('id_siswa'); // Simpan object absensi berdasarkan id_siswa

    $siswas = Siswa::where('id_kelas', $jadwal->id_kelas)
        ->orderBy('nama_siswa')
        ->get();

    return view('absensi.create', compact('jadwal', 'siswas', 'isToday', 'isWithinTime', 'alreadyAbsen'));
}
    /**
     * Simpan absensi (batch atau single).
     */
    public function store(Request $request)
{
    // 1. Ambil ID Jadwal & Tanggal
    $id_jadwal = $request->id_jadwal_mengajar;
    $today = \Carbon\Carbon::today('Asia/Jakarta')->toDateString();

    // 2. Validasi: Pastikan ada data keterangan yang dikirim
    if (!$request->has('keterangan')) {
        return redirect()->back()->with('error', 'Pilih minimal satu status absensi.');
    }

    // 3. Looping data keterangan [id_siswa => status]
    foreach ($request->keterangan as $id_siswa => $status) {
        
        // Gunakan updateOrCreate supaya jika guru klik simpan berkali-kali, 
        // data lama diupdate, bukan bikin baris baru yang double.
        \App\Models\Absensi::updateOrCreate(
            [
                'id_siswa'           => $id_siswa,
                'id_jadwal_mengajar' => $id_jadwal,
                'tanggal'            => $today,
            ],
            [
                'keterangan' => $status,
                // Logika Jam Masuk: 
                // Jika status 'hadir', isi jam sekarang (jika sebelumnya masih kosong)
                // Jika status bukan 'hadir', jam masuk dikosongkan (null)
                'jam_masuk'  => ($status == 'hadir') ? (now('Asia/Jakarta')->format('H:i:s')) : null,
                'jam_keluar' => ($status == 'hadir') ? (now('Asia/Jakarta')->format('H:i:s')) : null,
            ]
        );
    }

    // 4. Kembali ke halaman index daftar absensi
    return redirect()->route('guru.absensi.index', ['id_jadwal_mengajar' => $id_jadwal])
                     ->with('success', 'Absensi berhasil disimpan untuk seluruh siswa!');
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
    // 1. Ambil data Guru yang login
    $user = Auth::user();
    $idGuruLogin = $user->guru->id_guru ?? null;

    // 2. Filter dropdown Kelas: Hanya tampilkan kelas yang ada di jadwal mengajar guru tersebut
    $allKelas = Kelas::whereHas('jadwalMengajar', function($q) use ($idGuruLogin) {
            if ($idGuruLogin) {
                $q->where('id_guru', $idGuruLogin);
            }
        })
        ->orderBy('nama_kelas')
        ->get();
    
    $selKelasId = request()->query('kelas_id'); 
    $year = $year ?? now()->year;
    $month = $month ?? now()->month;

    // JIKA tidak ada kelas yang dipilih, tampilkan halaman awal (kosong)
    if (!$selKelasId) {
        return view('absensi.rekap_materialized', [
            'rekap' => null, 
            'allKelas' => $allKelas, 
            'year' => $year, 
            'month' => $month,
            'kelas' => null, 
            'mapels' => [], 
            'selKelas' => null,
            'selMapel' => null,
            'currentMapelName' => null
        ]);
    }

    // JIKA ADA kelas yang dipilih, proses datanya
    $kelas = Kelas::with('siswa')->findOrFail($selKelasId);
    $mapelId = request()->query('mapel_id');
    
    // 3. Filter dropdown Mapel: Hanya tampilkan mapel yang diajar guru tersebut di kelas yang dipilih
    $mapelIds = JadwalMengajar::where('id_kelas', $selKelasId)
                ->when($idGuruLogin, function($q) use ($idGuruLogin) {
                    return $q->where('id_guru', $idGuruLogin);
                })
                ->pluck('id_mapel')
                ->unique()
                ->filter()
                ->values()
                ->all();
                
    $mapels = MataPelajaran::whereIn('id_mata_pelajaran', $mapelIds)
              ->orderBy('nama_mapel')
              ->get();

    $currentMapelName = $mapelId ? optional(MataPelajaran::find($mapelId))->nama_mapel : null;

    // 4. Ambil data rekap (pastikan fungsi collectRekapWithFilters juga mendukung filter guru/mapel)
    $data = $this->collectRekapWithFilters($selKelasId, $year, $month, $mapelId);
    $rekap = $data['rekap']->unique('id_siswa');

    return view('absensi.rekap_materialized', [
        'kelas' => $kelas,
        'rekap' => $rekap,
        'year' => $year,
        'month' => $month,
        'mapels' => $mapels,
        'mapelId' => $mapelId,
        'selKelas' => $selKelasId,
        'selMapel' => $mapelId,
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

    // --- TAMBAHKAN BARIS INI ---
    $tanggalCetak = \Carbon\Carbon::now()->toDateString(); 
    // atau bisa juga: $tanggalCetak = now();
    // ---------------------------

    return view('absensi.rekap_by_jadwal', [
        'jadwal' => $jadwal,
        'rekap' => $data['rekap'],
        'year' => $year,
        'month' => $month,
        'tanggalCetak' => $tanggalCetak, // <-- Pastikan ini dikirim ke view
    ]);
}

public function exportPdfByJadwal($id_jadwal, $year, $month)
{
    // 1. Ambil data pendukung
    $jadwal = JadwalMengajar::with(['kelas', 'mataPelajaran', 'guru'])->findOrFail($id_jadwal);
    
    // Ambil tanggal cetak (hari ini)
    $tanggalCetak = \Carbon\Carbon::now()->translatedFormat('d F Y');
    
    // 2. AMBIL DATA REKAP (Logika harus sama dengan yang di View)
    // Gunakan helper yang sudah kamu buat agar konsisten
    $data = $this->collectRekapForJadwal($id_jadwal, $year, $month);
    $rekap = $data['rekap'];

    // 3. Ambil Identitas Guru untuk tanda tangan
    $user = Auth::user();
    $namaGuruLogin = $user->guru ? $user->guru->nama_guru : $user->name;
    $nipGuru = $user->guru ? $user->guru->nip : '..........................';

    // 4. Generate PDF
    $pdf = Pdf::loadView('absensi.rekap_by_jadwal_pdf', [ // Pastikan nama view ini benar
        'jadwal' => $jadwal,
        'rekap'  => $rekap,
        'year'   => $year,
        'month'  => $month,
        'namaGuruLogin' => $namaGuruLogin,
        'nipGuru' => $nipGuru,
        'tanggalCetak' => $tanggalCetak
    ])->setPaper('a4', 'portrait'); // Jika kolom sedikit, portrait saja biar rapi

    return $pdf->download("Rekap_Absen_{$jadwal->kelas->nama_kelas}.pdf");
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