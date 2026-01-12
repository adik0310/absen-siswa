<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::orderBy('nama_guru')->get();
        return view('admin.guru.index', compact('gurus'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_guru' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'id_users' => 'nullable|exists:users,id_users',
        ]);

        Guru::create($request->only(['nama_guru','nip','id_users']));
        return redirect()->route('admin.guru.index')->with('success','Guru berhasil ditambahkan.');
    }

    public function show($id)
    {
        $guru = Guru::findOrFail($id);
        return view('admin.guru.show', compact('guru'));
    }

    public function edit($id)
    {
        $guru = Guru::findOrFail($id);
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_guru' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'id_users' => 'nullable|exists:users,id_users',
        ]);

        $guru = Guru::findOrFail($id);
        $guru->update($request->only(['nama_guru','nip','id_users']));

        return redirect()->route('admin.guru.index')->with('success','Guru berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        $guru->delete();
        return redirect()->route('admin.guru.index')->with('success','Guru berhasil dihapus.');
    }
}
