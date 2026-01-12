<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->increments('id_role_permission');
            $table->unsignedInteger('id_role')->nullable();
            $table->unsignedInteger('id_permission')->nullable();

            $table->index('id_role');
            $table->index('id_permission');

            $table->foreign('id_role')
                ->references('id_role')->on('roles')
                ->onDelete('cascade');

            $table->foreign('id_permission')
                ->references('id_permissions')->on('permissions')
                ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            $table->dropForeign(['id_role']);
            $table->dropForeign(['id_permission']);
        });

        Schema::dropIfExists('role_permissions');
    }
}
