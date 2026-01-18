<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function showJadwal($id_kelas)
    {
        $kelas = Kelas::findOrFail($id_kelas);
        $allKelas = Kelas::all();
        $mataPelajaran = MataPelajaran::with(['jadwalMengajar' => function($query) use ($id_kelas) {
            $query->where('id_kelas', $id_kelas)->with('user', 'absensi.siswa');
        }])->get();
        return view('kelas.jadwal', compact('kelas', 'mataPelajaran', 'allKelas'));
    }
}
