<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $guruList = [
            ['nama' => 'Achmad Rukmaria, S.Pd.', 'nip' => 'GURU001'],
            ['nama' => 'Ahmad Bukhori, S.H., S.Pd.', 'nip' => 'GURU002'],
            ['nama' => 'Aini Nurfatwa, S.E.', 'nip' => 'GURU004'],
            ['nama' => 'Anisa Shaina Saviera, S.Hum.', 'nip' => 'GURU005'],
            ['nama' => 'Ayi Saefulloh, S.Pd.', 'nip' => 'GURU009'],
            ['nama' => 'Deasy Resnasari, S.Pd.', 'nip' => 'GURU010'],
            ['nama' => 'Delani Febrianti, S.Pd.', 'nip' => 'GURU011'], // Wali X-D
            ['nama' => 'Delina Mulyani, S.Sos.', 'nip' => 'GURU012'],
            ['nama' => 'Dra. Sumiati', 'nip' => 'GURU013'],
            ['nama' => 'H. Dadang Hermawan, S.E.', 'nip' => 'GURU014'],
            ['nama' => 'Hj. Didah Hamidah, S.Pd.', 'nip' => 'GURU017'],
            ['nama' => 'Imam Zainuri, S.Pd.I.', 'nip' => 'GURU019'],
            ['nama' => 'Jihan Fadhilah, S.T.', 'nip' => 'GURU022'], // Wali X-B
            ['nama' => 'KH. Shofwan Aly, M.Pd.I.', 'nip' => 'GURU024'],
            ['nama' => 'Lathifah Shofiani, S.Ag.', 'nip' => 'GURU025'],
            ['nama' => 'Mohamad Puad Syafi\'i, M.A.', 'nip' => 'GURU026'],
            ['nama' => 'Nur Asiah Jamilah, S.Pd.', 'nip' => 'GURU028'],
            ['nama' => 'Nurkholis, S.Pd.', 'nip' => 'GURU029'],
            ['nama' => 'Purwanti, S.Pd.', 'nip' => 'GURU030'],
            ['nama' => 'Putri Alicia', 'nip' => 'GURU099'],
            ['nama' => 'Rahmat Hidayat, M.Pd.', 'nip' => 'GURU031'],
            ['nama' => 'Reni Yuliani, S.Sos.', 'nip' => 'GURU033'],
            ['nama' => 'Rustandi', 'nip' => 'GURU038'],
            ['nama' => 'Sera Afriyanti, S.Pd.', 'nip' => 'GURU039'], // Wali X-A
            ['nama' => 'Shifa Vita Kharomah, S.Pd.', 'nip' => 'GURU040'],
            ['nama' => 'Silviana Intan Saharini, S.Pd.', 'nip' => 'GURU053'],
            ['nama' => 'Sri Rahayu Anggaraeni, S.Pd.', 'nip' => 'GURU043'],
            ['nama' => 'Veni Ayu Rahmayanti, S.Pd.', 'nip' => 'GURU044'],
            ['nama' => 'Wildan Arip Abdillah, S.Pd.', 'nip' => 'GURU045'],
            ['nama' => 'Wulan Permanasari, S.Pd.', 'nip' => 'GURU046'],
            ['nama' => 'Yasril Ahmad Syairazi, S.Pd.', 'nip' => 'GURU047'], // Wali X-C
            ['nama' => 'Lia Dwi Arsilya', 'nip' => 'GURU048'],
            ['nama' => 'Noknia', 'nip' => 'GURU050'],
            ['nama' => 'Muhammad Fahim Nurul Haqi', 'nip' => 'GURU051'],
            ['nama' => 'Muhamad Ibnu Sina', 'nip' => 'GURU052'],
            ['nama' => 'Mustagitsul Aziez Pangestu, S.Pd.', 'nip' => 'GURU061'],
            ['nama' => 'H. Encep Rachman, S.Pd.', 'nip' => 'GURU062'],
            ['nama' => 'Ikhsanudin Yusup, S.Pd.', 'nip' => 'GURU063'], 
        ];

        // Pemetaan Wali Kelas (Nama Guru => Nama Kelas)
        $waliKelasMap = [
            'Sera Afriyanti, S.Pd.' => 'X-A',
            'Jihan Fadhilah, S.T.' => 'X-B',
            'Yasril Ahmad Syairazi, S.Pd.' => 'X-C',
            'Delani Febrianti, S.Pd.' => 'X-D',
        ];

        Schema::disableForeignKeyConstraints();
        DB::table('guru')->truncate();

        foreach ($guruList as $index => $g) {
            // 1. Insert Guru dan ambil ID-nya
            $idGuru = DB::table('guru')->insertGetId([
                'nama_guru' => $g['nama'],
                'nip' => $g['nip'],
                'id_users' => $index + 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Jika guru ini terdaftar sebagai wali, update tabel kelas
            if (isset($waliKelasMap[$g['nama']])) {
                $namaKelas = $waliKelasMap[$g['nama']];
                
                DB::table('kelas')
                    ->where('nama_kelas', $namaKelas)
                    ->update([
                        'id_guru' => $idGuru, // Sesuaikan nama kolom ini dengan tabel kelas kamu
                        'updated_at' => now()
                    ]);
            }
        }

        Schema::enableForeignKeyConstraints();
        echo "GuruSeeder: Berhasil memasukkan data guru dan wali kelas.\n";
    }
}