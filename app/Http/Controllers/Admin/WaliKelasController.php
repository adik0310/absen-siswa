<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Guru;

class WaliKelasController extends Controller
{
    public function index()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $guruList = Guru::orderBy('nama_guru')->get();
        
        return view('admin.wali.index', compact('kelasList', 'guruList'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required',
            'id_guru' => 'nullable' // Boleh kosong kalau mau hapus wali kelas
        ]);

        $kelas = Kelas::findOrFail($request->id_kelas);
        $kelas->id_guru = $request->id_guru;
        $kelas->save();

        return redirect()->back()->with('success', 'Wali Kelas berhasil diperbarui!');
    }
}