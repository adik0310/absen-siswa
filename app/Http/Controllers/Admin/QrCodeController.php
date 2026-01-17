<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal; // Pastikan Model Jadwal sudah ada
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function siswaIndex(Request $request)
{
    // Bisa difilter per kelas biar gak pusing
    $kelas = $request->get('kelas');
    $siswa = \App\Models\Siswa::when($kelas, function($query) use ($kelas){
                return $query->where('kelas', $kelas);
            })->get();

    return view('admin.qrcode.siswa', compact('siswa'));
}

public function printCard($id)
{
    $siswa = \App\Models\Siswa::findOrFail($id);
    
    // QR Code berisi NIS/ID Siswa
    $qrcode = QrCode::size(150)->generate($siswa->nis); 

    return view('admin.qrcode.print-card', compact('qrcode', 'siswa'));
}
// app/Http/Controllers/Admin/QrCodeController.php

public function showCard($id)
{
    $siswa = \App\Models\Siswa::findOrFail($id);
    
    // QR Code berisi NIS
    $qrcode = QrCode::size(200)->generate($siswa->nis); 

    return view('admin.qrcode.show-card', compact('qrcode', 'siswa'));
}
}