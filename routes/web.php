<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
// Admin controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JadwalMengajarController as AdminJadwalController;
use App\Http\Controllers\Admin\RekapController as AdminRekapController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\GuruLoginController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MataPelajaranController as AdminMataPelajaranController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\WaliKelasController as AdminWaliKelasController;

use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\JadwalMengajarController as GuruJadwalMengajarController;
use App\Http\Controllers\Guru\AbsensiController as GuruAbsensiController;
use App\Http\Controllers\Guru\WaliKelasController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

Route::middleware(['auth'])->group(function () {

    Route::get('/profile', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // ADMIN
    Route::prefix('admin')->name('admin.')->middleware(['role.admin'])->group(function () {

            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            // // QrCode
            Route::get('/qrcode-siswa', [QrCodeController::class, 'siswaIndex'])->name('qrcode.siswa');
            Route::get('/qrcode-siswa/print/{id}', [QrCodeController::class, 'printCard'])->name('qrcode.print-card');
            Route::get('/qrcode-siswa/show/{id}', [QrCodeController::class, 'showCard'])->name('qrcode.show-card');
            Route::get('/qrcode-siswa/print/{id}', [QrCodeController::class, 'printCard'])->name('qrcode.print-card');

            // mapel
            Route::get('mapel', [AdminMataPelajaranController::class, 'index'])->name('mapel.index');
            Route::get('mapel/create', [AdminMataPelajaranController::class, 'create'])->name('mapel.create');
            Route::post('mapel/store', [AdminMataPelajaranController::class, 'store'])->name('mapel.store');
            Route::get('mapel/{id}', [AdminMataPelajaranController::class, 'show'])->name('mapel.show');
            Route::get('mapel/{id}/edit', [AdminMataPelajaranController::class, 'edit'])->name('mapel.edit');
            Route::put('mapel/{id}/update', [AdminMataPelajaranController::class, 'update'])->name('mapel.update');
            Route::delete('mapel/{id}/delete', [AdminMataPelajaranController::class, 'destroy'])->name('mapel.delete');

            // guru
            Route::get('guru', [GuruController::class, 'index'])->name('guru.index');
            Route::get('guru/create', [GuruController::class, 'create'])->name('guru.create');
            Route::post('guru/store', [GuruController::class, 'store'])->name('guru.store');
            Route::get('guru/{id}', [GuruController::class, 'show'])->name('guru.show');
            Route::get('guru/{id}/edit', [GuruController::class, 'edit'])->name('guru.edit');
            Route::put('guru/{id}/update', [GuruController::class, 'update'])->name('guru.update');
            Route::delete('guru/{id}/delete', [GuruController::class, 'destroy'])->name('guru.delete');

            //wali kelas
            Route::get('/wali-kelas', [AdminWaliKelasController::class, 'index'])->name('wali.index');
            Route::post('/wali-kelas/update', [AdminWaliKelasController::class, 'update'])->name('wali.update');

            // siswa
            Route::get('siswa', [SiswaController::class, 'index'])->name('siswa.index');
            Route::get('siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
            Route::post('siswa/store', [SiswaController::class, 'store'])->name('siswa.store');
            Route::get('siswa/{id}', [SiswaController::class, 'show'])->name('siswa.show');
            Route::get('siswa/{id}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
            Route::put('siswa/{id}/update', [SiswaController::class, 'update'])->name('siswa.update');
            Route::delete('siswa/{id}/delete', [SiswaController::class, 'destroy'])->name('siswa.delete');

            // jadwal admin
            Route::get('jadwal', [AdminJadwalController::class, 'index'])->name('jadwal.index');
            Route::get('jadwal/create', [AdminJadwalController::class, 'create'])->name('jadwal.create');
            Route::post('jadwal/store', [AdminJadwalController::class, 'store'])->name('jadwal.store');
            Route::get('jadwal/{id}', [AdminJadwalController::class, 'show'])->name('jadwal.show');
            Route::get('jadwal/{id}/edit', [AdminJadwalController::class, 'edit'])->name('jadwal.edit');
            Route::put('jadwal/{id}/update', [AdminJadwalController::class, 'update'])->name('jadwal.update');
            Route::delete('jadwal/{id}/delete', [AdminJadwalController::class, 'destroy'])->name('jadwal.delete');

            // kelas admin
            Route::get('kelas', [KelasController::class, 'index'])->name('kelas.index');
            Route::get('kelas/create', [KelasController::class, 'create'])->name('kelas.create');
            Route::post('kelas', [KelasController::class, 'store'])->name('kelas.store');
            Route::get('kelas/{id}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
            Route::put('kelas/{id}', [KelasController::class, 'update'])->name('kelas.update');
            Route::delete('kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.destroy');
            Route::get('kelas/{id}', [KelasController::class, 'show'])->name('kelas.show');

            // admin rekaps & absensi admina
            Route::get('kelas/{id_kelas}/mapel', [AdminRekapController::class, 'getMapelForKelas'])->name('admin.kelas.mapel');

            Route::get('absensi', [AdminAbsensiController::class, 'index'])->name('absensi.index');
            Route::get('absensi/create', [AdminAbsensiController::class, 'create'])->name('absensi.create');
            Route::post('absensi', [AdminAbsensiController::class, 'store'])->name('absensi.store');
            Route::get('absensi/{id}/edit', [AdminAbsensiController::class, 'edit'])->name('absensi.edit');
            Route::put('absensi/{id}', [AdminAbsensiController::class, 'update'])->name('absensi.update');
            Route::delete('absensi/{id}', [AdminAbsensiController::class, 'destroy'])->name('absensi.destroy');
            Route::get('absensi/jadwal/{id}', [AdminAbsensiController::class, 'byJadwal'])->name('absensi.byJadwal');
            Route::get('/get-guru-filter', [AdminAbsensiController::class, 'getGuruByJadwal'])->name('get.guru.jadwal');

            // Route Khusus Fetch (Filter Otomatis)
            Route::get('/rekap/get-mapel/{id_kelas}', [AdminRekapController::class, 'getMapelForKelas']);
            Route::get('/rekap/get-guru/{id_kelas}/{id_mapel}', [AdminRekapController::class, 'getGuruForMapelKelas']);

            // Route Rekap
            Route::get('/rekap', [AdminRekapController::class, 'index'])->name('rekap.index');
            Route::get('/rekap/{id_kelas}/{year}/{month}', [AdminRekapController::class, 'show'])->name('rekap.show');
            Route::post('/rekap/pdf', [AdminRekapController::class, 'exportPdf'])->name('rekap.pdf');
            Route::post('/rekap/excel', [AdminRekapController::class, 'exportExcel'])->name('rekap.excel');

            Route::get('guru-login', [GuruLoginController::class, 'index'])->name('guru.login.index');
            Route::post('guru-login/sync', [GuruLoginController::class, 'sync'])->name('guru.login.sync');
            Route::post('guru-login/{id}/reset', [GuruLoginController::class, 'reset'])->name('guru.login.reset');
        });

   // ----- GURU AREA -----
    Route::prefix('guru')->name('guru.')->middleware(['role.guru'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');

        Route::get('/rekap-wali-kelas', [WaliKelasController::class, 'rekapKelas'])->name('rekap.wali');
        Route::get('/rekap-absensi/detail/{id_siswa}', [WaliKelasController::class, 'detailRekapSiswa'])->name('rekap.detail');
        // Pastikan diletakkan di dalam group middleware guru agar aman
        Route::get('/rekap-wali-kelas/pdf', [WaliKelasController::class, 'exportPdf'])->name('rekap.pdf');
        //scan
        // Route untuk menampilkan halaman scanner
        Route::get('/absensi/scan/{id_jadwal_mengajar}', [GuruAbsensiController::class, 'scan'])->name('absensi.scan');
        Route::post('/absensi/scan-proses/{id_jadwal_mengajar}', [GuruAbsensiController::class, 'storeScan'])->name('absensi.scan_proses');
        // Jadwal guru
        Route::get('/jadwal', [GuruJadwalMengajarController::class, 'index'])->name('jadwal.index');
        // List absensi hari ini untuk jadwal tertentu
        Route::get('/jadwal/{id_jadwal_mengajar}/absensi', [GuruAbsensiController::class, 'index'])->name('absensi.index');
        // Form input absensi
        Route::get('/jadwal/{id_jadwal_mengajar}/absensi/create',[GuruAbsensiController::class, 'create'])->name('absensi.create');
        // Submit absensi (batch atau single)
        Route::post('/jadwal/{id_jadwal_mengajar}/absensi',[GuruAbsensiController::class, 'store'])->name('absensi.store');
        // Edit item absensi tertentu
        Route::get('/absensi/{id}/edit',[GuruAbsensiController::class, 'edit'])->name('absensi.edit');
        // Update absensi
        Route::put('/absensi/{id}',[GuruAbsensiController::class, 'update'])->name('absensi.update');
        // Hapus absensi
        Route::delete('/absensi/{id}',[GuruAbsensiController::class, 'destroy'])->name('absensi.destroy');
        // Generate rekap (materialisasi)
        Route::post('/rekap/generate/{id_kelas?}/{year?}/{month?}',[GuruAbsensiController::class, 'generateMonthlyRekap'])->name('absensi.rekap.generate');
        // Lihat rekap
        Route::get('/rekap/{id_kelas?}/{year?}/{month?}',[GuruAbsensiController::class, 'viewMonthlyRekap'])->name('absensi.rekap');
        // export
        Route::get('/kelas/{id_kelas?}/rekap/pdf/{year?}/{month?}', [GuruAbsensiController::class, 'exportMonthlyRekapPdf'])->name('absensi.rekap.pdf');
        Route::get('/kelas/{id_kelas?}/rekap/excel/{year?}/{month?}', [GuruAbsensiController::class, 'exportMonthlyRekapExcel'])->name('absensi.rekap.excel');
        // ReKap by jadwal
        Route::get('/jadwal/{id_jadwal}/rekap', [GuruAbsensiController::class, 'viewRekapByJadwal'])->name('absensi.rekap.by_jadwal');
        // PDF download (DomPDF)
        Route::get('/jadwal/{id_jadwal}/rekap/pdf/{year?}/{month?}', [GuruAbsensiController::class, 'exportPdfByJadwal'])->name('absensi.rekap.pdf_download_by_jadwal');
        // Excel download
        Route::get('/jadwal/{id_jadwal}/rekap/excel/{year?}/{month?}', [GuruAbsensiController::class, 'exportExcelByJadwal'])->name('absensi.rekap.excel_by_jadwal');
    });

});
