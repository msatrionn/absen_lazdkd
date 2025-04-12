<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\StaffController;
use App\Models\Jabatan;
use App\Models\Jadwal;
use App\Models\User;

// Route::get('/', function () {
// });



Route::get('/login', [AuthController::class, 'index']);
Route::get('/register', [AuthController::class, 'index_register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::middleware(['web'])->group(function () {
    // AUTH
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

    // JABATAN
    Route::get('/dashboard/jabatan', [JabatanController::class, 'index'])->name('jabatan.index');
    Route::get('/dashboard/jabatan/data', [JabatanController::class, 'getData'])->name('jabatan.data');
    Route::post('/dashboard/jabatan', [JabatanController::class, 'store'])->name('jabatan.store');
    Route::get('/dashboard/jabatan/{id}/edit', [JabatanController::class, 'edit'])->name('jabatan.edit');
    Route::put('/dashboard/jabatan/{id}', [JabatanController::class, 'update'])->name('jabatan.update');
    Route::delete('/dashboard/jabatan/{id}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');
    Route::get('/jabatan/list', function () {
        return response()->json(Jabatan::select('id', 'nama_jabatan')->get());
    })->name('jabatan.list');

    // JADWAL
    Route::get('/dashboard/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/dashboard/jadwal/data', [JadwalController::class, 'getData'])->name('jadwal.data');
    Route::post('/dashboard/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::get('/dashboard/jadwal/{id}/edit', [JadwalController::class, 'edit'])->name('jadwal.edit');
    Route::put('/dashboard/jadwal/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/dashboard/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    Route::get('/user/list', function () {
        return response()->json(User::Join('staff', 'staff.id_user', 'users.id')->select('users.id', 'staff.nama')->get());
    })->name('user.list');


    // staff
    Route::get('/dashboard/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/dashboard/staff/data', [StaffController::class, 'getData'])->name('staff.data');
    Route::post('/dashboard/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/dashboard/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/dashboard/staff/{id}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/dashboard/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
    // ABSEN
    Route::get('/', [AbsenController::class, 'index'])->name('home.absen')->middleware('auth');
    Route::get('/history', [AbsenController::class, 'showHistory'])->name('absen.showHistory')->middleware('auth');
    Route::get('/dashboard/absen', [AbsenController::class, 'show_absen'])->middleware('auth');
    Route::post('/absensi/store', [AbsenController::class, 'store'])->name('absensi.store');
});
