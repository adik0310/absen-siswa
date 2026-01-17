<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\JadwalMengajar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class WaliKelasController extends Controller
{
    // Menampilkan Daftar Siswa (Halaman Utama)
public function rekapKelas(Request $request)
{
    $user = Auth::user();
    $guru = Guru::where('id_users', $user->id_users)->first();
    $kelas = Kelas::where('id_guru', $guru->id_guru)->first();

    if (!$kelas) return redirect()->back()->with('error', 'Anda bukan wali kelas.');

    // Fitur Pencarian Nama
    $search = $request->get('search');
    $siswas = Siswa::where('id_kelas', $kelas->id_kelas)
                ->when($search, function($query) use ($search) {
                    return $query->where('nama_siswa', 'like', "%{$search}%");
                })
                ->orderBy('nama_siswa')
                ->get();

    return view('guru.rekap_wali', compact('kelas', 'siswas'));
}

// Menampilkan Detail Rekap per Siswa (Halaman Detail)
public function detailRekapSiswa(Request $request, $id_siswa)
{
    $month = (int) $request->get('month', date('m'));
    $year = (int) $request->get('year', date('Y'));
    
    $siswa = Siswa::findOrFail($id_siswa);
    $kelas = Kelas::findOrFail($siswa->id_kelas);

    $mapels = JadwalMengajar::where('id_kelas', $kelas->id_kelas)
                ->with('mataPelajaran')->get()
                ->pluck('mataPelajaran')->unique('id_mata_pelajaran')->values();

    $absensi = Absensi::where('id_siswa', $id_siswa)
                ->with('jadwal')
                ->whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();

    $maxPertemuan = 12;

    return view('guru.rekap_detail_siswa', compact('siswa', 'kelas', 'absensi', 'month', 'year', 'mapels', 'maxPertemuan'));
}

    public function exportPdf(Request $request)
    {
        $id_siswa = $request->get('id_siswa');
        $month = (int) $request->get('month', date('m'));
        $year = (int) $request->get('year', date('Y'));

        $user = Auth::user();
        $guru = Guru::where('id_users', $user->id_users)->first();
        $siswa = Siswa::findOrFail($id_siswa);
        $kelas = Kelas::where('id_kelas', $siswa->id_kelas)->first();

        $mapels = JadwalMengajar::where('id_kelas', $kelas->id_kelas)
                    ->with('mataPelajaran')
                    ->get()
                    ->pluck('mataPelajaran')
                    ->unique('id_mata_pelajaran')
                    ->values();

        // Load relasi jadwal untuk identifikasi mapel di PDF
        $absensi = Absensi::where('id_siswa', $id_siswa)
                    ->with('jadwal')
                    ->whereMonth('tanggal', $month)
                    ->whereYear('tanggal', $year)
                    ->get();

        $maxPertemuan = 12;

        $pdf = Pdf::loadView('guru.rekap_pdf', compact(
            'kelas', 'guru', 'siswa', 'mapels', 'absensi', 'month', 'year', 'maxPertemuan'
        ))->setPaper([0, 0, 609.45, 935.43], 'portrait'); // Ukuran F4 Landscape

        return $pdf->download("rekap_absen_{$siswa->nama_siswa}.pdf");
    }
}