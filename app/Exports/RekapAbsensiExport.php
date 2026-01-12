<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapAbsensiExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.rekap_absensi_excel', $this->data);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Membuat semua teks rata tengah secara vertikal
            'A:Z' => ['alignment' => ['vertical' => 'center']],
            // Membuat Header Tabel Bold
            '7'   => ['font' => ['bold' => true]],
            '8'   => ['font' => ['bold' => true]],
        ];
    }
}