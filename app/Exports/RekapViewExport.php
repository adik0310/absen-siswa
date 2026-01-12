<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle; // <--- Tambahkan ini

class RekapViewExport implements FromView, ShouldAutoSize, WithTitle // <--- Tambahkan WithTitle di sini
{
    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function view(): View
{
    return view('admin.cetak.rekap_excel', $this->payload);
}

public function title(): string
{
    return 'Rekap Absensi';
}

}