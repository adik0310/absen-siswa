<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MataPelajaran;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        // Query builder untuk MataPelajaran
        $query = MataPelajaran::query();

        // Filter berdasarkan search (nama mapel)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_mapel', 'like', '%' . $search . '%');
        }

        // Urutkan berdasarkan nama mapel
        $query->orderBy('nama_mapel');

        // Paginate hasil query (10 per halaman, sesuaikan jika perlu)
        $mapels = $query->paginate(10);

        return view('admin.mapel.index', compact('mapels'));
    }

    public function create()
    {
        return view('admin.mapel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
        ]);

        MataPelajaran::create([
            'nama_mapel' => $request->nama_mapel,
        ]);

        return redirect()->route('admin.mapel.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function show($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        return view('admin.mapel.show', compact('mapel'));
    }

    public function edit($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        return view('admin.mapel.edit', compact('mapel'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
        ]);

        $mapel = MataPelajaran::findOrFail($id);
        $mapel->update([
            'nama_mapel' => $request->nama_mapel,
        ]);

        return redirect()->route('admin.mapel.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        $mapel->delete();

        return redirect()->route('admin.mapel.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}