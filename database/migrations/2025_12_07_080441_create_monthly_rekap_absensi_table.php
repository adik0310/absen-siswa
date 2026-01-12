<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyRekapAbsensiTable extends Migration
{
    public function up()
    {
        Schema::create('monthly_rekap_absensi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_kelas')->nullable(false);
            $table->unsignedInteger('id_siswa')->nullable(false);
            $table->year('year')->nullable(false);
            $table->tinyInteger('month')->nullable(false); // 1..12

            // counters
            $table->unsignedSmallInteger('hadir')->default(0);
            $table->unsignedSmallInteger('sakit')->default(0);
            $table->unsignedSmallInteger('izin')->default(0);
            $table->unsignedSmallInteger('alfa')->default(0);
            $table->unsignedSmallInteger('total')->default(0);

            // optional denormalized data for convenience
            $table->string('nama_siswa', 120)->nullable();
            $table->string('nis', 50)->nullable();
            $table->string('nama_kelas', 50)->nullable();

            $table->unique(['id_kelas','id_siswa','year','month'], 'uniq_monthly_rekap'); // buat upsert mudah
            $table->index(['id_kelas','year','month']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('monthly_rekap_absensi');
    }
}
