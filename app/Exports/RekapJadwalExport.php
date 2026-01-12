<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RekapJadwalExport implements FromView, ShouldAutoSize
{
    // 1. Daftarkan dulu variabel yang mau dipakai
    protected $jadwal;
    protected $rekap;
    protected $year;
    protected $month;
    protected $namaGuruLogin;
    protected $nipGuru;

    // 2. Isi variabel tersebut saat Class dipanggil
    public function __construct($data) 
    { 
        $this->jadwal = $data['jadwal'];
        $this->rekap = $data['rekap'];
        $this->year = $data['year'];
        $this->month = $data['month'];
        $this->namaGuruLogin = $data['namaGuruLogin'];
        $this->nipGuru = $data['nipGuru'];
    }

    public function view(): View
    {
        // Sekarang $this->year dan $this->month sudah ada isinya!
        $carbonMonth = Carbon::createFromDate((int)$this->year, (int)$this->month, 1);
        $daysInMonth = $carbonMonth->daysInMonth;

        return view('exports.rekap_jadwal_excel', [
            'jadwal' => $this->jadwal,
            'rekap' => $this->rekap,
            'year' => $this->year,
            'month' => $this->month,
            'daysInMonth' => $daysInMonth,
            'namaGuruLogin' => $this->namaGuruLogin,
            'nipGuru' => $this->nipGuru
        ]);
    }
}