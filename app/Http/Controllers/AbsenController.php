<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AbsensiDetail;
use App\Models\Jadwal;
use App\Models\Lokasi;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

use function PHPUnit\Framework\isNull;

class AbsenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cekJadwal = Jadwal::where('id_user', 1)->first();
        $cekAbsenMasuk = Absensi::join('staff', 'staff.id', 'absensi.id_staff')
            ->join('users', 'users.id', 'staff.id_user')
            ->join('absensi_detail', 'absensi_detail.id_absen', 'absensi.id')
            ->where('absensi.id_staff', 1)
            ->where('absensi_detail.status_absen', 'masuk')
            ->whereDate('absensi_detail.created_at', Carbon::now())
            ->first();
        $cekAbsenKeluar = Absensi::join('staff', 'staff.id', 'absensi.id_staff')
            ->join('users', 'users.id', 'staff.id_user')
            ->join('absensi_detail', 'absensi_detail.id_absen', 'absensi.id')
            ->where('absensi.id_staff', 1)
            ->where('absensi_detail.status_absen', 'pulang')
            ->whereDate('absensi_detail.created_at', Carbon::now())
            ->first();
        $flexibleMasuk = true;
        $flexiblePulang = true;
        if ($cekJadwal->is_flexible == 0 && is_null($cekAbsenMasuk)) {
            $flexibleMasuk = true;
        } else if ($cekJadwal->is_flexible == 0 && $cekAbsenMasuk) {
            $flexibleMasuk = false;
        } else if ($cekJadwal->is_flexible == 1) {
            $flexibleMasuk = true;
        }
        if ($cekJadwal->is_flexible == 0 && is_null($cekAbsenKeluar)) {
            $flexiblePulang = true;
        } else if ($cekJadwal->is_flexible == 0 && $cekAbsenKeluar) {
            $flexiblePulang = false;
        } else if ($cekJadwal->is_flexible == 1) {
            $flexiblePulang = true;
        }
        return view('/absensi', compact('flexibleMasuk', 'flexiblePulang'));
    }

    public function dashboard()
    {
        $totalStaff = Staff::count();
        $jumlahHadirHariIni = Staff::count() - (Staff::count() - Absensi::whereDate('tgl_absen', today())->count());

        $labels = [];
        $data = [];
        $jumlahTerlambat = DB::table('absensi_detail')
            ->join('jadwal', 'absensi_detail.id_jadwal', '=', 'jadwal.id')
            ->whereRaw('TIME(absensi_detail.waktu_absen) >= jadwal.jam_masuk')
            ->count();


        foreach (range(6, 0) as $i) {
            $tanggal = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('D');
            $data[] = Absensi::rightJoin('absensi_detail', 'absensi_detail.id_absen', 'absensi.id')->whereDate('tgl_absen', $tanggal)->count();
        }
        $jumlahBelumAbsen = Staff::count() - Absensi::whereDate('tgl_absen', today())->count();

        return view('dashboard', compact('totalStaff', 'jumlahTerlambat', 'jumlahBelumAbsen', 'jumlahHadirHariIni', 'labels', 'data'));
    }

    public function dashboardAbsen()
    {
        return view('riwayat_absen.index');
    }

    public function getHistoryAbsen(Request $request)
    {
        $query = DB::table('absensi')
            ->select(
                'staff.id as id_staff',
                'staff.nama',
                'absensi.tgl_absen',
                DB::raw('jadwal_masuk.jam_masuk'),
                DB::raw('jadwal_masuk.jam_keluar'),
                DB::raw('TIME(masuk.waktu_absen) as waktu_masuk'),
                DB::raw('masuk.file_url as file_url_masuk'),
                DB::raw('lokasi_masuk.detail_lokasi as lokasi_masuk'),
                DB::raw('TIME(pulang.waktu_absen) as waktu_pulang'),
                DB::raw('pulang.file_url as file_url_pulang'),
                DB::raw('lokasi_pulang.detail_lokasi as lokasi_pulang')
            )
            ->join('staff', 'staff.id', '=', 'absensi.id_staff')
            ->leftJoin(DB::raw("
            (SELECT * FROM absensi_detail WHERE status_absen = 'masuk') as masuk
        "), 'masuk.id_absen', '=', 'absensi.id')
            ->leftJoin(DB::raw("
            (SELECT * FROM absensi_detail WHERE status_absen = 'pulang') as pulang
        "), 'pulang.id_absen', '=', 'absensi.id')
            ->leftJoin('lokasi as lokasi_masuk', 'lokasi_masuk.id', '=', 'masuk.id_lokasi')
            ->leftJoin('lokasi as lokasi_pulang', 'lokasi_pulang.id', '=', 'pulang.id_lokasi')
            ->leftJoin('jadwal as jadwal_masuk', 'jadwal_masuk.id', '=', 'masuk.id_jadwal')
            // kalau perlu juga bisa join jadwal_pulang kalau misalnya beda jadwal
            ->groupBy(
                'staff.id',
                'staff.nama',
                'absensi.tgl_absen',
                'jadwal_masuk.jam_masuk',
                'jadwal_masuk.jam_keluar',
                'masuk.waktu_absen',
                'masuk.file_url',
                'lokasi_masuk.detail_lokasi',
                'pulang.waktu_absen',
                'pulang.file_url',
                'lokasi_pulang.detail_lokasi'
            );

        return DataTables::of($query)
            ->filter(function ($instance) use ($request) {
                if ($request->has('search') && $request->search['value'] != '') {
                    $search = $request->search['value'];
                    $instance->where('staff.nama', 'like', "%" . $search . "%");
                }

                if ($request->has('start_date') && $request->start_date != '') {
                    $instance->where('absensi.tgl_absen', '>=', $request->start_date);
                }

                if ($request->has('end_date') && $request->end_date != '') {
                    $instance->where('absensi.tgl_absen', '<=', $request->end_date);
                }
            })
            ->make(true);
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
        $id_staff = Staff::where('id_user', Auth::user()->id)->first()->id;
        $request->validate([
            'status_absen' => 'required|string',
            'photo' => 'required|string',
            'nama_lokasi' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Simpan lokasi
        $lokasi = Lokasi::firstOrCreate([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ], [
            'detail_lokasi' => $request->nama_lokasi
        ]);

        // Simpan absensi (per hari)
        $absensi = Absensi::firstOrCreate([
            'tgl_absen' => Carbon::today(),
            'id_staff' => $id_staff
        ]);

        // Handle base64 image
        $photo = $request->photo;

        if (preg_match('/^data:image\/(\w+);base64,/', $photo, $type)) {
            $photo = substr($photo, strpos($photo, ',') + 1); // Buang metadata
            $extension = strtolower($type[1]); // Ambil extension

            if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
                return response()->json(['message' => 'Format foto tidak didukung!'], 400);
            }

            $photo = base64_decode($photo);
            if ($photo === false) {
                return response()->json(['message' => 'Foto gagal didecode!'], 400);
            }
        } else {
            return response()->json(['message' => 'Format foto tidak valid!'], 400);
        }

        $imageName = 'absen_' . time() . '.' . $extension;
        $path = "absensi/{$imageName}";

        // Simpan file
        $result = Storage::disk('public')->put($path, $photo);
        if (!$result) {
            return response()->json(['message' => 'Gagal simpan foto!'], 500);
        }

        $jadwal = Jadwal::where('id_user', Auth::id())->first()->id ?? null;

        AbsensiDetail::create([
            'id_absen' => $absensi->id,
            'id_lokasi' => $lokasi->id,
            'id_jadwal' => $jadwal,
            'waktu_absen' => Carbon::now(),
            'status_absen' => $request->status_absen,
            'file_name' => $imageName,
            'file_url' => $path,
        ]);

        return response()->json([
            'message' => 'Absensi berhasil disimpan!',
            'data' => [
                'file_url' => Storage::url($path),
                'file_path' => $path,
            ]
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
                DB::raw("GROUP_CONCAT(CASE WHEN status_absen = 'masuk' THEN detail_lokasi END SEPARATOR '||') as lokasi_absen"),
                DB::raw("GROUP_CONCAT(CASE WHEN status_absen = 'pulang' THEN detail_lokasi END SEPARATOR '||') as lokasi_pulang"),
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

            // Proses file_url_absen
            $item->file_url_absen = $item->file_url_absen ? array_map(function ($path) {
                // Bersihkan path dari karakter tidak perlu
                $cleanPath = trim($path);
                // Jika path sudah benar (relatif), gunakan langsung
                if (Storage::disk('public')->exists($cleanPath)) {
                    return Storage::url($cleanPath);
                }
                // Jika path mengandung 'public/', sesuaikan
                if (strpos($cleanPath, 'public/') === 0) {
                    $relativePath = str_replace('public/', '', $cleanPath);
                    if (Storage::disk('public')->exists($relativePath)) {
                        return Storage::url($relativePath);
                    }
                }
                return null; // Jika tidak ditemukan
            }, explode(',', $item->file_url_absen)) : [];

            // Proses file_url_pulang dengan cara yang sama
            $item->file_url_pulang = $item->file_url_pulang ? array_map(function ($path) {
                $cleanPath = trim($path);
                if (Storage::disk('public')->exists($cleanPath)) {
                    return Storage::url($cleanPath);
                }
                if (strpos($cleanPath, 'public/') === 0) {
                    $relativePath = str_replace('public/', '', $cleanPath);
                    if (Storage::disk('public')->exists($relativePath)) {
                        return Storage::url($relativePath);
                    }
                }
                return null;
            }, explode(',', $item->file_url_pulang)) : [];

            $item->lokasi_absen = $item->lokasi_absen ? explode('||', $item->lokasi_absen) : [];
            $item->lokasi_pulang = $item->lokasi_pulang ? explode('||', $item->lokasi_pulang) : [];
        }
        return view('/history', compact('absensi', 'from', 'to', 'nama'));
    }
}
