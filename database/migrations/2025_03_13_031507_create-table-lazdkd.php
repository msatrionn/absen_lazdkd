<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jabatan');
            $table->timestamps();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat');
            $table->string('telp');
            $table->foreignId('id_jabatan')->constrained('jabatan')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('lokasi', function (Blueprint $table) {
            $table->id();
            $table->decimal('longitude', 11, 8);
            $table->decimal('latitude', 11, 8);
            $table->text('detail_lokasi');
            $table->timestamps();
        });

        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_absen');
            $table->foreignId('id_staff')->constrained('staff')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('absensi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_absen')->constrained('absensi')->onDelete('cascade');
            $table->foreignId('id_lokasi')->constrained('lokasi')->onDelete('cascade');
            $table->datetime('waktu_absen');
            $table->string('status_absen');
            $table->string('file_name')->nullable();
            $table->string('file_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensi_detail');
        Schema::dropIfExists('absensi');
        Schema::dropIfExists('lokasi');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('jabatan');
    }
};
