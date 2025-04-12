<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'nip',
        'nama',
        'jenis_kelamin',
        'alamat',
        'telp',
        'id_jabatan',
        'id_user'
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
