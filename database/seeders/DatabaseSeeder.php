<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'User Biasa',
                'username' => 'user',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Seeder Jabatan
        DB::table('jabatan')->insert([
            ['nama_jabatan' => 'Manager', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jabatan' => 'Supervisor', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jabatan' => 'Staff', 'created_at' => now(), 'updated_at' => now()]
        ]);

        // Seeder Staff
        DB::table('staff')->insert([
            [
                'id' => 1, // Tambahkan ID secara eksplisit
                'nip' => '123456',
                'nama' => 'Budi Santoso',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Merdeka No. 1',
                'telp' => '08123456789',
                'id_jabatan' => 1, // Manager
                'id_user' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2, // Tambahkan ID secara eksplisit
                'nip' => '654321',
                'nama' => 'Siti Aisyah',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Sudirman No. 2',
                'telp' => '08234567890',
                'id_jabatan' => 2, // Supervisor
                'id_user' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Seeder Lokasi
        DB::table('lokasi')->insert([
            [
                'id' => 1, // Tambahkan ID
                'longitude' => 106.8166667,
                'latitude' => -6.2000000,
                'detail_lokasi' => 'Jakarta Pusat',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2, // Tambahkan ID
                'longitude' => 110.3750000,
                'latitude' => -7.8000000,
                'detail_lokasi' => 'Yogyakarta',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Seeder Absensi
        DB::table('absensi')->insert([
            [
                'id' => 1, // Tambahkan ID
                'tgl_absen' => Carbon::now()->toDateString(),
                'id_staff' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2, // Tambahkan ID
                'tgl_absen' => Carbon::now()->toDateString(),
                'id_staff' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Seeder Absensi Detail
        DB::table('absensi_detail')->insert([
            [
                'id' => 1, // Tambahkan ID
                'id_absen' => 1,
                'id_lokasi' => 1,
                'waktu_absen' => Carbon::now(),
                'status_absen' => 'Masuk',
                'file_name' => null,
                'file_url' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2, // Tambahkan ID
                'id_absen' => 2,
                'id_lokasi' => 2,
                'waktu_absen' => Carbon::now(),
                'status_absen' => 'Keluar',
                'file_name' => null,
                'file_url' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
