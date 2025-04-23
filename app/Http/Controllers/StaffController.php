<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class StaffController extends Controller
{
    public function index()
    {
        return view('staff.index');
    }

    public function getData()
    {
        $staff = Staff::with(['jabatan', 'user'])->where('id', '!=', 1)->select([
            'id',
            'nip',
            'nama',
            'jenis_kelamin',
            'alamat',
            'telp',
            'id_jabatan',
            'id_user'
        ])->orderBy("created_at", 'desc');

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
            // Validasi User
            'username' => 'required|string|max:16|unique:users',
            'password' => 'required|string|min:6|confirmed',

            // Validasi Staff
            'nip' => 'required|string|unique:staff,nip|max:255',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'telp' => 'required|string|max:15',
            'id_jabatan' => 'required|exists:jabatan,id',
        ]);

        // Buat user
        $user = User::create([
            'name' => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        // Buat staff (relasi ke user)
        Staff::create([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'telp' => $request->telp,
            'id_jabatan' => $request->id_jabatan,
            'id_user' => $user->id,
        ]);

        return response()->json(['success' => 'Staff berhasil ditambahkan']);
    }

    public function edit($id)
    {
        return response()->json(Staff::join("users", 'users.id', "staff.id_user")->find($id));
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

        Staff::where('id', $id)->update($request->except('_token', 'nip', 'id_user', 'jabatan'));

        return response()->json(['success' => 'Staff berhasil diperbarui']);
    }
    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|confirmed|min:6',
        ]);

        $staff = User::findOrFail($id);
        $staff->password = Hash::make($request->new_password);
        $staff->save();

        return response()->json(['success' => 'Password berhasil diubah']);
    }

    public function destroy($id)
    {
        Staff::destroy($id);
        return response()->json(['success' => 'Staff berhasil dihapus']);
    }
}
