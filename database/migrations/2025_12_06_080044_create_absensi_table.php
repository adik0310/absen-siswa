<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensiTable extends Migration
{
    public function up()
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->increments('id_absensi');
            $table->unsignedInteger('id_jadwal_mengajar')->nullable();
            $table->unsignedInteger('id_siswa')->nullable();
            $table->date('tanggal')->nullable();
            $table->enum('keterangan', ['hadir','sakit','izin','alfa'])->nullable();

            $table->index('id_jadwal_mengajar');
            $table->index('id_siswa');

            $table->foreign('id_jadwal_mengajar')->references('id_jadwal_mengajar')->on('jadwal_mengajar')->onDelete('cascade');

            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')
                ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropForeign(['id_jadwal_mengajar']);
            $table->dropForeign(['id_siswa']);
        });

        Schema::dropIfExists('absensi');
    }
}
