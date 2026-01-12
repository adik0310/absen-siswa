<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuruTable extends Migration
{
    public function up()
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->increments('id_guru');
            $table->string('nama_guru', 100)->nullable();
            $table->string('nip', 50)->nullable();
            $table->unsignedInteger('id_users')->nullable();

            $table->index('id_users');

            $table->foreign('id_users')
                ->references('id_users')->on('users')
                ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('guru', function (Blueprint $table) {
            $table->dropForeign(['id_users']);
        });
        Schema::dropIfExists('guru');
    }
}
