<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function view(): View
    {
        return view('admin.cetak.rekap_print', $this->payload);
    }

    public function title(): string
    {
        // Ambil nama kelas / bulan / tahun kalau ada
        $title = $this->payload['kelasName'] 
            ?? 'Rekap';

        // Hapus karakter ilegal Excel
        $title = str_replace(['\\', '/', '*', '[', ']', ':', '?'], '', $title);

        // Batasi 31 karakter
        return substr($title, 0, 31);
    }
}
