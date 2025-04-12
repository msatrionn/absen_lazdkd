<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AbsensiDetail;
use App\Models\Jadwal;
use App\Models\Lokasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AbsenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('/absensi');
    }

    public function show_absen()
    {
        return view('/tabel_absen');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_staff' => 'required|exists:staff,id',
            'status_absen' => 'required|string',
            'photo' => 'required|string',
            'nama_lokasi' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Simpan lokasi jika belum ada
        $lokasi = Lokasi::firstOrCreate([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ], [
            'detail_lokasi' => $request->nama_lokasi
        ]);

        // Simpan absensi (jika belum ada untuk hari ini)
        $absensi = Absensi::firstOrCreate([
            'tgl_absen' => Carbon::today(),
            'id_staff' => $request->id_staff
        ]);

        // Simpan foto ke storage
        $image = str_replace('data:image/png;base64,', '', $request->photo);
        $image = str_replace(' ', '+', $image);
        $imageName = 'absen_' . time() . '.png';
        Storage::put("public/absensi/{$imageName}", base64_decode($image));
        $jadwal = Jadwal::where('id_user', Auth::user()->id)->first()->id ?? null;
        $absensiDetail = AbsensiDetail::create([
            'id_absen' => $absensi->id,
            'id_lokasi' => $lokasi->id,
            'id_jadwal' => $jadwal,
            'waktu_absen' => Carbon::now(),
            'status_absen' => $request->status_absen,
            'file_name' => $imageName,
            'file_url' => Storage::url("public/absensi/{$imageName}"),
        ]);

        return response()->json([
            'message' => 'Absensi berhasil disimpan!',
            'data' => $absensiDetail
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function showHistory(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $nama = $request->input('nama');

        $user  = Auth::user()->username;
        $query = DB::table('absensi')
            ->select(
                'tgl_absen',
                DB::raw("GROUP_CONCAT(CASE WHEN status_absen = 'masuk' THEN TIME(waktu_absen) END) as absen"),
                DB::raw("GROUP_CONCAT(CASE WHEN status_absen = 'pulang' THEN TIME(waktu_absen) END) as pulang"),
                DB::raw("GROUP_CONCAT(CASE WHEN status_absen = 'masuk' THEN file_url END) as file_url_absen"),
                DB::raw("GROUP_CONCAT(CASE WHEN status_absen = 'pulang' THEN file_url END) as file_url_pulang"),
                DB::raw("GROUP_CONCAT(CASE WHEN status_absen = 'masuk' THEN detail_lokasi END) as lokasi_absen"),
                DB::raw("GROUP_CONCAT(CASE WHEN status_absen = 'pulang' THEN detail_lokasi END) as lokasi_pulang"),
                'jam_masuk',
                'jam_keluar'
            )
            ->leftJoin("absensi_detail", 'absensi_detail.id_absen', '=', 'absensi.id')
            ->leftJoin("staff", 'staff.id', '=', 'absensi.id_staff')
            ->leftJoin("users", 'users.id', '=', 'staff.id_user')
            ->leftJoin("jabatan", 'jabatan.id', '=', 'staff.id_jabatan')
            ->leftJoin("jadwal", 'jadwal.id', '=', 'absensi_detail.id_jadwal')
            ->leftJoin("lokasi", 'lokasi.id', '=', 'absensi_detail.id_lokasi')
            ->where('users.username', $user)
            ->groupBy('tgl_absen', 'jam_masuk', 'jam_keluar');


        if ($from) {
            $query->whereDate('tgl_absen', '>=', $from);
        }

        if ($to) {
            $query->whereDate('tgl_absen', '<=', $to);
        }


        $absensi = $query->orderBy('tgl_absen', 'desc')->paginate(10)->withQueryString();
        foreach ($absensi as $item) {
            $item->absen = $item->absen ? explode(',', $item->absen) : [];
            $item->pulang = $item->pulang ? explode(',', $item->pulang) : [];
            $item->file_url_absen = $item->file_url_absen ? explode(',', $item->file_url_absen) : [];
            $item->file_url_pulang = $item->file_url_pulang ? explode(',', $item->file_url_pulang) : [];
            $item->lokasi_absen = $item->lokasi_absen ? explode(',', $item->lokasi_absen) : [];
            $item->lokasi_pulang = $item->lokasi_pulang ? explode(',', $item->lokasi_pulang) : [];
        }
        return view('/history', compact('absensi', 'from', 'to', 'nama'));
    }
}
