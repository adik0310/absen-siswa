<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalMengajar;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\MataPelajaran;

class JadwalMengajarController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalMengajar::with(['kelas', 'mataPelajaran', 'guru']);

        // Logika pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('hari', 'like', '%' . $search . '%')
                  ->orWhere('ruangan', 'like', '%' . $search . '%')
                  ->orWhereHas('kelas', function ($subQ) use ($search) {
                      $subQ->where('nama_kelas', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('guru', function ($subQ) use ($search) {
                      $subQ->where('nama_guru', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('mataPelajaran', function ($subQ) use ($search) {
                      $subQ->where('nama_mapel', 'like', '%' . $search . '%');
                  });
            });
        }

        $jadwalList = $query->orderByRaw("FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jum\'at','Sabtu')")
                            ->orderBy('jam_mulai')
                            ->get();

        return view('admin.jadwal.index', compact('jadwalList'));
    }

    // Method lainnya tetap sama...
    public function create()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $guruList  = Guru::orderBy('nama_guru')->get();
        $mapelList = MataPelajaran::orderBy('nama_mapel')->get();

        return view('admin.jadwal.create', compact('kelasList','guruList','mapelList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|string|max:30',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => 'required|exists:mata_pelajaran,id_mata_pelajaran',
            'id_guru'  => 'required|exists:guru,id_guru',
            'ruangan'  => 'nullable|string|max:100',
        ]);

        JadwalMengajar::create([
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'id_kelas' => $request->id_kelas,
            'id_mapel' => $request->id_mapel,
            'id_guru'  => $request->id_guru,
            'ruangan'  => $request->ruangan,
        ]);

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function show($id)
    {
        $jadwal = JadwalMengajar::with(['kelas','mataPelajaran','guru'])->findOrFail($id);
        return view('admin.jadwal.show', compact('jadwal'));
    }

    public function edit($id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $guruList  = Guru::orderBy('nama_guru')->get();
        $mapelList = MataPelajaran::orderBy('nama_mapel')->get();

        return view('admin.jadwal.edit', compact('jadwal','kelasList','guruList','mapelList'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'hari' => 'required|string|max:30',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => 'required|exists:mata_pelajaran,id_mata_pelajaran',
            'id_guru'  => 'required|exists:guru,id_guru',
            'ruangan'  => 'nullable|string|max:100',
        ]);

        $jadwal = JadwalMengajar::findOrFail($id);
        $jadwal->update([
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'id_kelas' => $request->id_kelas,
            'id_mapel' => $request->id_mapel,
            'id_guru'  => $request->id_guru,
            'ruangan'  => $request->ruangan,
        ]);

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}