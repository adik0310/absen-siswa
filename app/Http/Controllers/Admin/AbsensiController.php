<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\JadwalMengajar;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;

class AbsensiController extends Controller
{
    public function index(Request $request)
{
    $absensi = Absensi::with([
            'siswa',
            'jadwal.kelas',
            'jadwal.mataPelajaran',
            'jadwal.guru'
        ])
        ->when($request->id_kelas, fn ($q) =>
            $q->whereHas('jadwal', fn ($j) =>
                $j->where('id_kelas', $request->id_kelas)
            )
        )
        ->when($request->id_mapel, fn ($q) =>
            $q->whereHas('jadwal', fn ($j) =>
                $j->where('id_mapel', $request->id_mapel)
            )
        )
        ->when($request->id_guru, fn ($q) =>
            $q->whereHas('jadwal', fn ($j) =>
                $j->where('id_guru', $request->id_guru)
            )
        )
        ->orderBy('tanggal', 'desc')
        ->paginate(10)
        ->appends($request->query());

    return view('admin.absensi.index', [
        'absensi' => $absensi,
        'kelas'   => Kelas::orderBy('nama_kelas')->get(),
        'mapel'   => MataPelajaran::orderBy('nama_mapel')->get(),
        'guru'    => Guru::orderBy('nama_guru')->get(),
    ]);
}

    public function create()
    {
        return view('admin.absensi.create', [
            'jadwal' => JadwalMengajar::with('kelas','mataPelajaran','guru')->get(),
            'siswa'  => Siswa::orderBy('nama_siswa')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_jadwal_mengajar' => 'required|exists:jadwal_mengajar,id_jadwal_mengajar',
            'id_siswa'           => 'required|exists:siswa,id_siswa',
            'tanggal'            => 'required|date',
            'keterangan'         => 'required|string',
            'keterangan_detail'  => 'nullable|string',
        ]);

        Absensi::create($data);
        return redirect()->route('admin.absensi.index')->with('success', 'Data berhasil ditambah.');
    }

    public function edit($id)
    {
        return view('admin.absensi.edit', [
            'row'    => Absensi::findOrFail($id),
            'jadwal' => JadwalMengajar::with('kelas','mataPelajaran','guru')->get(),
            'siswa'  => Siswa::orderBy('nama_siswa')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'id_jadwal_mengajar' => 'required|exists:jadwal_mengajar,id_jadwal_mengajar',
            'id_siswa'           => 'required|exists:siswa,id_siswa',
            'tanggal'            => 'required|date',
            'keterangan'         => 'required|string',
            'keterangan_detail'  => 'nullable|string',
        ]);

        Absensi::findOrFail($id)->update($data);
        return redirect()->route('admin.absensi.index')->with('success','Data diperbarui.');
    }

    public function destroy($id)
    {
        Absensi::findOrFail($id)->delete();
        return redirect()->route('admin.absensi.index')->with('success','Data dihapus.');
    }
}
