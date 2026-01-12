<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        // Query builder untuk Siswa dengan relasi kelas
        $query = Siswa::with('kelas');

        // Filter berdasarkan search (nama siswa atau NIS)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_siswa', 'like', '%' . $search . '%')
                  ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan kelas
        if ($request->filled('id_kelas')) {
            $query->where('id_kelas', $request->id_kelas);
        }

        // Filter berdasarkan jenis kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Urutkan berdasarkan nama siswa
        $query->orderBy('nama_siswa');

        // Paginate hasil query (10 per halaman, sesuaikan jika perlu)
        $siswas = $query->paginate(10);

        // Ambil data kelas untuk dropdown filter
        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('admin.siswa.index', compact('siswas', 'kelas'));
    }

    public function create()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        return view('admin.siswa.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nis' => 'required|string|max:50|unique:siswa,nis',
            'jenis_kelamin' => 'required|in:L,P',
            'id_kelas' => 'required|exists:kelas,id_kelas',
        ]);

        Siswa::create($request->only(['nama_siswa','nis','jenis_kelamin','id_kelas']));
        return redirect()->route('admin.siswa.index')->with('success','Siswa berhasil ditambahkan.');
    }

    public function show($id)
    {
        $siswa = Siswa::with('kelas')->findOrFail($id);
        return view('admin.siswa.show', compact('siswa'));
    }

    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        return view('admin.siswa.edit', compact('siswa','kelasList'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nis' => 'required|string|max:50|unique:siswa,nis,'.$siswa->id_siswa.',id_siswa',
            'jenis_kelamin' => 'required|in:L,P',
            'id_kelas' => 'required|exists:kelas,id_kelas',
        ]);

        $siswa->update($request->only(['nama_siswa','nis','jenis_kelamin','id_kelas']));
        return redirect()->route('admin.siswa.index')->with('success','Siswa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success','Siswa berhasil dihapus.');
    }
}