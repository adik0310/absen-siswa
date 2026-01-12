<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id_users');
            // $table->string('username', 50)->unique();
            $table->string('email', 50)->unique();
            $table->string('nama', 50)->nullable();
            $table->string('password', 255)->nullable();
            $table->unsignedInteger('id_role')->nullable();

            $table->index('id_role');

            $table->foreign('id_role')
                ->references('id_role')->on('roles')
                ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_role']);
        });
        Schema::dropIfExists('users');
    }
}
