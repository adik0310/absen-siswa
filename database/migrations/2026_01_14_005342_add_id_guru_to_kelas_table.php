<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('kelas', function (Blueprint $table) {
        // Gunakan unsignedInteger (BUKAN unsignedBigInteger) supaya cocok dengan 'increments'
        $table->unsignedInteger('id_guru')->nullable()->after('nama_kelas');

        // Tambahkan hubungan (Foreign Key)
        $table->foreign('id_guru')
              ->references('id_guru')
              ->on('guru')
              ->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('kelas', function (Blueprint $table) {
        // Hapus foreign key dulu baru hapus kolomnya
        $table->dropForeign(['id_guru']);
        $table->dropColumn('id_guru');
    });
}
};
