<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\MataPelajaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exports\RekapViewExport; // Pastikan file ini ada jika ingin export Excel
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
        $mapelIds = DB::table('jadwal_mengajar')
            ->where('id_kelas', $id_kelas)
            ->pluck('id_mapel')
            ->unique();

        $mapelRows = DB::table('mata_pelajaran')
            ->whereIn('id_mata_pelajaran', $mapelIds)
            ->select('id_mata_pelajaran as id_mapel', 'nama_mapel')
            ->orderBy('nama_mapel')
            ->get();

        return response()->json($mapelRows);
    }

    public function getGuruForMapelKelas($id_kelas, $id_mapel)
    {
        $guruIds = DB::table('jadwal_mengajar')
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $id_mapel)
            ->pluck('id_guru')
            ->unique();

        $guruRows = DB::table('guru')
            ->whereIn('id_guru', $guruIds)
            ->select('id_guru', 'nama_guru')
            ->orderBy('nama_guru')
            ->get();

        return response()->json($guruRows);
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

    // ==========================================
    // FUNGSI EXPORT PDF (YANG TADI HILANG)
    // ==========================================
    public function exportPdf(Request $request)
    {
        // Ambil data dari request (form post)
        $id_kelas = $request->id_kelas;
        $year     = $request->year;
        $month    = $request->month;
        $id_mapel = $request->mapel;
        $id_guru  = $request->guru;

        // Gunakan buildRekapPayload yang sudah ada untuk ambil data
        $data = $this->buildRekapPayload($id_kelas, $year, $month, $id_mapel, $id_guru);

        // Load view khusus untuk PDF (pastikan kamu punya file ini: resources/views/admin/rekap/pdf.blade.php)
        $pdf = Pdf::loadView('admin.cetak.rekap_print', $data)->setPaper('a4', 'landscape');
        
        $filename = 'rekap-absensi-' . Str::slug($data['kelasName']) . '-' . $month . '-' . $year . '.pdf';
        
        return $pdf->download($filename);
    }

    // ==========================================
    // FUNGSI EXPORT EXCEL
    // ==========================================
    public function exportExcel(Request $request)
    {
        $id_kelas = $request->id_kelas;
        $year     = $request->year;
        $month    = $request->month;
        $id_mapel = $request->mapel;
        $id_guru  = $request->guru;

        $data = $this->buildRekapPayload($id_kelas, $year, $month, $id_mapel, $id_guru);
        
        $filename = 'rekap-absensi-' . Str::slug($data['kelasName']) . '-' . $month . '-' . $year . '.xlsx';

        return Excel::download(new RekapViewExport($data), $filename);
    }

    protected function buildRekapPayload(int $id_kelas, int $year, int $month, $id_mapel = null, $id_guru = null): array
    {
        $id_mapel = ($id_mapel == "0" || !$id_mapel) ? null : $id_mapel;
        $id_guru = ($id_guru == "0" || !$id_guru) ? null : $id_guru;

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $kelas = Kelas::find($id_kelas);
        $mapel = $id_mapel ? DB::table('mata_pelajaran')->where('id_mata_pelajaran', $id_mapel)->first() : null;
        $guru = $id_guru ? Guru::find($id_guru) : null;

        $query = Absensi::select('absensi.*')
            ->join('jadwal_mengajar', 'absensi.id_jadwal_mengajar', '=', 'jadwal_mengajar.id_jadwal_mengajar')
            ->where('jadwal_mengajar.id_kelas', $id_kelas);

        if ($id_mapel) $query->where('jadwal_mengajar.id_mapel', $id_mapel);
        if ($id_guru)  $query->where('jadwal_mengajar.id_guru',  $id_guru);

        $absensi = $query->whereBetween('absensi.tanggal', [$start, $end])->get();
        $summaryMap = $absensi->groupBy('id_siswa');
        $siswaList = Siswa::where('id_kelas', $id_kelas)->orderBy('nama_siswa')->get();

        $rekapData = $siswaList->map(function ($s) use ($summaryMap) {
            $rows = $summaryMap->get($s->id_siswa, collect([]));
            return [
                'siswa'  => $s,
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
            'daysInMonth'  => $start->daysInMonth,
        ];
    }
}