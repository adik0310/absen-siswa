<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalMengajar;
use App\Models\Guru;

class JadwalMengajarController extends Controller
{
    public function index()
{
    $user = Auth::user();
    $query = JadwalMengajar::with(['mataPelajaran', 'kelas', 'guru']);

    // Filter guru
    if ($user) {
        $guru = Guru::where('id_users', $user->id_users)->first();
        $query->where('id_guru', $guru ? $guru->id_guru : $user->id_users);
    }

    $jadwals = $query
        ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
        ->orderBy('jam_mulai')
        ->get();

    // --- BAGIAN PENTING: Ambil ID yang jadwalnya HARI INI ---
    $todayName = strtolower(\Carbon\Carbon::now()->locale('id')->isoFormat('dddd'));
    $allowedJadwalIds = $jadwals->filter(function ($item) use ($todayName) {
        return strtolower(trim($item->hari)) === $todayName;
    })->pluck('id_jadwal_mengajar')->toArray();

    return view('guru.jadwal_index', compact('jadwals', 'allowedJadwalIds'));
}
}
