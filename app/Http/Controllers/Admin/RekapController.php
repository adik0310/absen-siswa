<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\JadwalMengajar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exports\RekapViewExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class RekapController extends Controller
{
    public function index()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $guru  = Guru::orderBy('nama_guru')->get();
        return view('admin.rekap.index', compact('kelas', 'guru'));
    }

    public function getMapelForKelas($id_kelas)
    {
        $id_kelas = (int) $id_kelas;
        $mapelIds = DB::table('jadwal_mengajar')
            ->where('id_kelas', $id_kelas)
            ->pluck('id_mapel')
            ->unique()
            ->filter()
            ->values()
            ->all();

        if (empty($mapelIds)) return response()->json([]);

        $mapelRows = DB::table('mata_pelajaran')
            ->whereIn('id_mata_pelajaran', $mapelIds)
            ->select('id_mata_pelajaran', 'nama_mapel')
            ->orderBy('nama_mapel')
            ->get();

        return response()->json($mapelRows->map(function ($m) {
            return [
                'id_mapel'   => $m->id_mata_pelajaran,
                'nama_mapel' => $m->nama_mapel,
            ];
        }));
    }

    public function show($id_kelas, $year = null, $month = null)
    {
        $year  = $year  ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;

        $payload = $this->buildRekapPayload(
            (int)$id_kelas,
            (int)$year,
            (int)$month,
            request()->mapel,
            request()->guru
        );

        return view('admin.rekap.show', $payload);
    }

    protected function buildRekapPayload(int $id_kelas, int $year, int $month, $id_mapel = null, $id_guru = null): array
{
    // Standarisasi ID (Menghindari string "0")
    $id_mapel = ($id_mapel == "0" || !$id_mapel) ? null : $id_mapel;
    $id_guru = ($id_guru == "0" || !$id_guru) ? null : $id_guru;

    $start = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
    $end = (clone $start)->endOfMonth();

    $kelas = \App\Models\Kelas::find($id_kelas);
    
    // AMBIL DATA GURU DAN MAPEL SECARA EKSPLISIT
    $mapel = $id_mapel ? DB::table('mata_pelajaran')->where('id_mata_pelajaran', $id_mapel)->first() : null;
    $guru = $id_guru ? \App\Models\Guru::find($id_guru) : null;

    $query = \App\Models\Absensi::select('absensi.*')
        ->join('jadwal_mengajar', 'absensi.id_jadwal_mengajar', '=', 'jadwal_mengajar.id_jadwal_mengajar')
        ->where('jadwal_mengajar.id_kelas', $id_kelas);

    if ($id_mapel) $query->where('jadwal_mengajar.id_mapel', $id_mapel);
    if ($id_guru)  $query->where('jadwal_mengajar.id_guru',  $id_guru);

    $absensi = $query->whereBetween('absensi.tanggal', [$start, $end])->get();

    $summaryMap = $absensi->groupBy('id_siswa');
    $siswaList = \App\Models\Siswa::where('id_kelas', $id_kelas)->orderBy('nama_siswa')->get();

    $rekapData = $siswaList->map(function ($s) use ($summaryMap, $year, $month) {
        $rows = $summaryMap->get($s->id_siswa, collect([]));
        
        $harian = [];
        foreach ($rows as $row) {
            // KUNCI: Gunakan format Y-m-d agar sinkron dengan Blade Excel
            $tglKey = \Carbon\Carbon::parse($row->tanggal)->format('Y-m-d');
            $harian[$tglKey] = strtoupper(substr($row->keterangan, 0, 1));
        }

        return [
            'siswa'  => $s,
            'harian' => $harian,
            'hadir'  => $rows->where('keterangan', 'hadir')->count(),
            'sakit'  => $rows->where('keterangan', 'sakit')->count(),
            'izin'   => $rows->where('keterangan', 'izin')->count(),
            'alfa'   => $rows->where('keterangan', 'alfa')->count(),
        ];
    });

    return [
        'rekapData'    => $rekapData,
        'id_kelas'     => $id_kelas,
        'year'         => $year,
        'month'        => $month,
        'mapelId'      => $id_mapel ?? 0,
        'guruId'       => $id_guru ?? 0,
        'kelasName'    => $kelas->nama_kelas ?? '-',
        'mapelName'    => $mapel->nama_mapel ?? 'Semua Mata Pelajaran',
        'guruName'     => $guru->nama_guru ?? '-',
        'guruNip'      => $guru->nip ?? '-',
        'daysInMonth'  => $start->daysInMonth,
        'tanggalCetak' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
    ];
}
    public function exportPdf(Request $request)
    {
        try {
            $payload = $this->buildRekapPayload(
                (int)$request->id_kelas,
                (int)$request->year,
                (int)$request->month,
                $request->mapel,
                $request->guru
            );

            // Perhatikan: Pastikan nama view cetak Anda benar
            $pdf = Pdf::loadView('admin.cetak.rekap_print', $payload) 
                ->setPaper('a4', 'landscape');

            return $pdf->stream('rekap.pdf');
        } catch (\Exception $e) {
            return "Gagal Cetak PDF: " . $e->getMessage();
        }
    }

    public function exportExcel(Request $request)
    {
        $payload = $this->buildRekapPayload(
            (int)$request->id_kelas,
            (int)$request->year,
            (int)$request->month,
            $request->mapel,
            $request->guru
        );

        $fileName = "rekap-absensi-" . Str::slug($payload['kelasName']) . "-" . $payload['month'] . ".xlsx";
        return Excel::download(new RekapViewExport($payload), $fileName);
    }
}