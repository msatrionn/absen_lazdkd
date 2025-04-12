<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StaffController extends Controller
{
    public function index()
    {
        return view('staff.index');
    }

    public function getData()
    {
        $staff = Staff::with(['jabatan', 'user'])->select([
            'id',
            'nip',
            'nama',
            'jenis_kelamin',
            'alamat',
            'telp',
            'id_jabatan',
            'id_user'
        ]);

        return DataTables::of($staff)
            ->addColumn('jabatan', function ($staff) {
                return $staff->jabatan->nama_jabatan ?? '-';
            })
            ->addColumn('user', function ($staff) {
                return $staff->user->name ?? '-';
            })
            ->addColumn('action', function ($staff) {
                return '
                    <button class="btn btn-sm btn-warning edit" data-id="' . $staff->id . '">Edit</button>
                    <button class="btn btn-sm btn-danger delete" data-id="' . $staff->id . '">Hapus</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|unique:staff,nip|max:255',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'telp' => 'required|string|max:15',
            'id_jabatan' => 'required|exists:jabatan,id',
            'id_user' => 'required|exists:users,id'
        ]);

        Staff::create($request->all());

        return response()->json(['success' => 'Staff berhasil ditambahkan']);
    }

    public function edit($id)
    {
        return response()->json(Staff::find($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'telp' => 'required|string|max:15',
            'id_jabatan' => 'required|exists:jabatan,id',
        ]);

        Staff::where('id', $id)->update($request->except('_token', 'nip', 'id_user'));

        return response()->json(['success' => 'Staff berhasil diperbarui']);
    }

    public function destroy($id)
    {
        Staff::destroy($id);
        return response()->json(['success' => 'Staff berhasil dihapus']);
    }
}
