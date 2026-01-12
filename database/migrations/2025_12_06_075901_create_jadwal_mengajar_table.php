<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalMengajarTable extends Migration
{
    public function up()
    {
        Schema::create('jadwal_mengajar', function (Blueprint $table) {
            $table->increments('id_jadwal_mengajar');

            $table->unsignedInteger('id_kelas')->nullable();
            $table->unsignedInteger('id_mapel')->nullable();
            $table->unsignedInteger('id_guru')->nullable();

            $table->string('hari', 20)->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('ruangan', 80)->nullable();

            $table->index('id_kelas');
            $table->index('id_mapel');
            $table->index('id_guru');

            $table->foreign('id_kelas')
                ->references('id_kelas')->on('kelas')
                ->onDelete('cascade');

            $table->foreign('id_mapel')
                ->references('id_mata_pelajaran')->on('mata_pelajaran')
                ->onDelete('cascade');

            $table->foreign('id_guru')
                ->references('id_guru')->on('guru')
                ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('jadwal_mengajar', function (Blueprint $table) {
            $table->dropForeign(['id_kelas']);
            $table->dropForeign(['id_mapel']);
            $table->dropForeign(['id_guru']);
        });

        Schema::dropIfExists('jadwal_mengajar');
    }
}
