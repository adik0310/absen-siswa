<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMataPelajaranTable extends Migration
{
    public function up()
    {
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->increments('id_mata_pelajaran');
            $table->string('nama_mapel', 100);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mata_pelajaran');
    }
}
