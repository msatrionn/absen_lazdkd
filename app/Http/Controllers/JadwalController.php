<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use Yajra\DataTables\Facades\DataTables;


class JadwalController extends Controller
{
    public function index()
    {
        return view('jadwal.index');
    }

    public function getData()
    {
        $jadwal = Jadwal::leftJoin('users', 'users.id', '=', 'jadwal.id_user')->leftJoin('staff', 'staff.id_user', '=', 'users.id')->select(['jadwal.id', 'jadwal.jam_masuk', 'jadwal.jam_keluar', 'staff.nama']);
        return DataTables::of($jadwal)
            ->addColumn('action', function ($jadwal) {
                return '
                    <button class="btn btn-sm btn-warning edit" data-id="' . $jadwal->id . '">Edit</button>
                    <button class="btn btn-sm btn-danger delete" data-id="' . $jadwal->id . '">Hapus</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|unique:jadwal,id_user',
            'jam_masuk' => 'required',
            'jam_keluar' => 'required'
        ], [
            'id_user.required' => 'Pilih user terlebih dahulu.',
            'id_user.unique' => 'User ini sudah memiliki jadwal!',
            'jam_masuk.required' => 'Jam masuk wajib diisi.',
            'jam_keluar.required' => 'Jam keluar wajib diisi.'
        ]);

        Jadwal::create([
            'id_user' => $request->id_user,
            'jam_masuk' => $request->jam_masuk,
            'jam_keluar' => $request->jam_keluar
        ]);

        return response()->json(['success' => 'Jadwal berhasil ditambahkan']);
    }

    public function edit($id)
    {
        return response()->json(Jadwal::find($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['jam_masuk' => 'required', 'jam_keluar' => 'required']);

        Jadwal::where('id', $id)->update([
            'jam_masuk' => $request->jam_masuk,
            'jam_keluar' => $request->jam_keluar
        ]);

        return response()->json(['success' => 'Jadwal berhasil diperbarui']);
    }

    public function destroy($id)
    {
        Jadwal::destroy($id);
        return response()->json(['success' => 'Jadwal berhasil dihapus']);
    }
}
