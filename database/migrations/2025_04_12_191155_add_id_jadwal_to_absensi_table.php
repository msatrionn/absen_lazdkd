<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('absensi_detail', function (Blueprint $table) {
            $table->foreignId('id_jadwal')
                ->after('id_absen')
                ->nullable()
                ->constrained('jadwal');
        });
    }

    public function down()
    {
        Schema::table('absensi_detail', function (Blueprint $table) {
            $table->dropForeign(['id_jadwal']);
            $table->dropColumn('id_jadwal');
        });
    }
};
