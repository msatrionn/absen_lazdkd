<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiDetail extends Model
{
    protected $table = 'absensi_detail';
    protected $fillable = ['id_absen', 'id_lokasi', 'id_jadwal', 'waktu_absen', 'status_absen', 'file_name', 'file_url'];
}
