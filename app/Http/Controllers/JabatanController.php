<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jabatan;
use Yajra\DataTables\Facades\DataTables;


class JabatanController extends Controller
{
    public function index()
    {
        return view('jabatan.index');
    }

    public function getData()
    {
        $jabatan = Jabatan::select(['id', 'nama_jabatan']);
        return DataTables::of($jabatan)
            ->addColumn('action', function ($jabatan) {
                return '
                    <button class="btn btn-sm btn-warning edit" data-id="' . $jabatan->id . '">Edit</button>
                    <button class="btn btn-sm btn-danger delete" data-id="' . $jabatan->id . '">Hapus</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate(['nama_jabatan' => 'required|string|max:255']);

        Jabatan::create(['nama_jabatan' => $request->nama_jabatan]);

        return response()->json(['success' => 'Jabatan berhasil ditambahkan']);
    }

    public function edit($id)
    {
        return response()->json(Jabatan::find($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_jabatan' => 'required|string|max:255']);

        Jabatan::where('id', $id)->update(['nama_jabatan' => $request->nama_jabatan]);

        return response()->json(['success' => 'Jabatan berhasil diperbarui']);
    }

    public function destroy($id)
    {
        Jabatan::destroy($id);
        return response()->json(['success' => 'Jabatan berhasil dihapus']);
    }
}
