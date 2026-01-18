<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiswaTable extends Migration
{
    public function up()
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->increments('id_siswa');
            $table->string('nama_siswa', 80)->nullable();
            $table->string('nis', 20)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->unsignedInteger('id_kelas')->nullable();

            $table->index('id_kelas');

            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['id_kelas']);
        });

        Schema::dropIfExists('siswa');
    }
}
