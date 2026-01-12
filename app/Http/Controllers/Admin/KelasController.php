<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Kelas;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('admin.kelas.index', compact('kelas'));
    }

    public function create()
    {
        return view('admin.kelas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:100|unique:kelas,nama_kelas',
        ]);

        Kelas::create(['nama_kelas' => $request->nama_kelas]);
        return redirect()->route('admin.kelas.index')->with('success','Kelas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        return view('admin.kelas.edit', compact('kelas'));
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $request->validate([
            'nama_kelas' => 'required|string|max:100|unique:kelas,nama_kelas,'.$kelas->id_kelas.',id_kelas',
        ]);

        $kelas->update(['nama_kelas' => $request->nama_kelas]);
        return redirect()->route('admin.kelas.index')->with('success','Kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();
        return redirect()->route('admin.kelas.index')->with('success','Kelas berhasil dihapus.');
    }
}
