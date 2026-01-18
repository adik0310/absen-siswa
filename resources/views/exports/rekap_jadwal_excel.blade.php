@php
    use Carbon\Carbon;
    $monthInt = (int)$month;
    $yearInt = (int)$year;
    
    // Total kolom tetap 7: No, Nama Lengkap, NIS, H, S, I, A
    $totalColumns = 7;
    $namaBulan = Carbon::createFromDate($yearInt, $monthInt, 1)->translatedFormat('F');
    $tanggalSekarang = Carbon::now()->translatedFormat('d F Y');
@endphp

<table>
    {{-- KOP SURAT --}}
    <tr>
        <th colspan="{{ $totalColumns }}" style="text-align: center; font-weight: bold; font-size: 14pt;">REKAPITULASI ABSENSI SISWA HARIAN</th>
    </tr>
    <tr>
        <th colspan="{{ $totalColumns }}" style="text-align: center; font-weight: bold; font-size: 16pt;">MADRASAH ALIYAH NURUL IMAN</th>
    </tr>
    <tr>
        <th colspan="{{ $totalColumns }}" style="text-align: center; font-size: 10pt; font-style: italic;">Jalan Cibaduyut Raya Blok TVRI III, Kota Bandung, Jawa Barat</th>
    </tr>
    <tr><td colspan="{{ $totalColumns }}" style="border-bottom: 2px solid #000;"></td></tr>
    <tr><td colspan="{{ $totalColumns }}"></td></tr>

    {{-- INFO JADWAL --}}
    <tr>
        <td style="font-weight: bold;">Mata Pelajaran</td>
        <td colspan="2">: {{ $jadwal->mataPelajaran->nama_mapel }}</td>
        <td colspan="2"></td> 
        <td style="font-weight: bold; text-align: right;">Tanggal Cetak</td>
        <td>: {{ $tanggalSekarang }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Kelas</td>
        <td colspan="2">: {{ $jadwal->kelas->nama_kelas }}</td>
        <td colspan="2"></td>
        <td style="font-weight: bold; text-align: right;">Tahun Ajaran</td>
        <td>: {{ $yearInt }}/{{ $yearInt + 1 }}</td>
    </tr>
    <tr><td colspan="{{ $totalColumns }}"></td></tr>

    {{-- HEADER TABEL --}}
    <thead>
        <tr>
            <th style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold; width: 40px;">NO</th>
            <th style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold; width: 300px;">NAMA LENGKAP SISWA</th>
            <th style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold; width: 120px;">NIS</th>
            <th style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold; width: 50px;">H</th>
            <th style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold; width: 50px;">S</th>
            <th style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold; width: 50px;">I</th>
            <th style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold; width: 50px;">A</th>
        </tr>
    </thead>

    {{-- ISI DATA --}}
    <tbody>
        @foreach($rekap as $index => $r)
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000; padding-left: 5px;">{{ strtoupper($r->nama_siswa) }}</td>
            {{-- Menggunakan petik agar NIS tetap terbaca teks di Excel --}}
            <td style="border: 1px solid #000; text-align: center;">{{ $r->nis }}</td>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $r->hadir }}</td>
            <td style="border: 1px solid #000; text-align: center; color: {{ $r->sakit > 0 ? '#000' : '#d3d3d3' }};">{{ $r->sakit }}</td>
            <td style="border: 1px solid #000; text-align: center; color: {{ $r->izin > 0 ? '#000' : '#d3d3d3' }};">{{ $r->izin }}</td>
            <td style="border: 1px solid #000; text-align: center; color: {{ $r->alfa > 0 ? '#FF0000' : '#d3d3d3' }}; font-weight: {{ $r->alfa > 0 ? 'bold' : 'normal' }};">
                {{ $r->alfa }}
            </td>
        </tr>
        @endforeach
    </tbody>

    {{-- TANDA TANGAN --}}
    <tr><td colspan="{{ $totalColumns }}"></td></tr>
    <tr>
        <td colspan="4"></td>
        <td colspan="3" style="text-align: center;">Bandung, {{ $tanggalSekarang }}</td>
    </tr>
    <tr>
        <td colspan="4"></td>
        <td colspan="3" style="text-align: center;">Guru Mata Pelajaran,</td>
    </tr>
    <tr><td colspan="{{ $totalColumns }}" style="height: 40px;"></td></tr>
    <tr>
        <td colspan="4"></td>
        <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $namaGuruLogin }}</td>
    </tr>
    <tr>
        <td colspan="4"></td>
        <td colspan="3" style="text-align: center;">NIP. {{ $nipGuru }}</td>
    </tr>
</table>