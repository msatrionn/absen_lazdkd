<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .badge {
            font-size: 0.85rem;
            padding: 6px 10px;
        }

        table img {
            transition: transform 0.2s ease;
            cursor: zoom-in;
        }

        table img:hover {
            transform: scale(1.6);
            z-index: 100;
            position: relative;
        }
    </style>
</head>

<body>

    <div class="container mt-5 mb-5">
        <a href="{{ route('home.absen') }}" class="text-decoration-none text-primary d-inline-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M15 8a.5.5 0 0 1-.5.5H2.707l4.147 4.146a.5.5 0 0 1-.708.708l-5-5a.5.5 0 0 1 0-.708l5-5a.5.5 0 1 1 .708.708L2.707 7.5H14.5A.5.5 0 0 1 15 8z" />
            </svg>
            Absen
        </a>

        <h3 class="mb-4 mt-4">📅 Riwayat Absensi</h3>

        <!-- Filter -->
        <form class="row g-3 mb-4" method="GET" action="{{ route('absen.showHistory') }}">
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" class="form-control" name="from" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" class="form-control" name="to" value="{{ request('to') }}">
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('absen.showHistory') }}" class="btn btn-secondary w-100">Clear</a>
            </div>
        </form>



        @php use Illuminate\Support\Facades\Storage; @endphp
        <!-- Tabel Absensi -->
        <div class="table-responsive shadow rounded">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th style="width: 180px">Tanggal</th>
                        <th>Jadwal Masuk</th>
                        <th style="width: 180px">Absen Masuk</th>
                        <th>Jadwal Pulang</th>
                        <th>Absen Pulang</th>
                        <th style="width: 180px">Absen Pulang</th>
                        <th>Status Pulang</th>
                        <th>Detail</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($absensi as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tgl_absen)->translatedFormat('D, d F Y') }}
                            </td>
                            <td>{{ $item->jam_masuk }}</td>

                            {{-- Absen --}}
                            <td>
                                @foreach ($item->absen as $keyAbsen => $absen)
                                    @if ($keyAbsen == 0)
                                        <b>{{ $absen }}</b>
                                    @else
                                        {{ ',' . $absen }}
                                    @endif
                                @endforeach
                            </td>

                            {{-- Jam Keluar --}}
                            <td>{{ $item->jam_keluar }}</td>

                            {{-- Pulang --}}
                            <td>
                                @foreach ($item->pulang as $keyAbsen => $pulang)
                                    @if ($keyAbsen === array_key_last($item->pulang))
                                        <b>{{ $pulang }}</b>
                                    @else
                                        {{ $pulang }},
                                    @endif
                                @endforeach
                            </td>

                            {{-- Status Absen --}}
                            <td>
                                @php
                                    $waktuAbsenPertama = $item->absen[0] ?? null;
                                    $statusAbsen =
                                        $waktuAbsenPertama && $waktuAbsenPertama > $item->jam_masuk
                                            ? 'Terlambat'
                                            : 'Tepat Waktu';
                                @endphp
                                <span class="badge bg-{{ $statusAbsen == 'Terlambat' ? 'warning' : 'success' }}">
                                    {{ $statusAbsen }}
                                </span>
                            </td>

                            {{-- Status Pulang --}}
                            <td>
                                @php
                                    $waktuPulangTerakhir = end($item->pulang);
                                    $statusPulang =
                                        $waktuPulangTerakhir && $waktuPulangTerakhir < $item->jam_keluar
                                            ? 'Pulang Cepat'
                                            : 'Tepat Waktu';
                                @endphp
                                <span class="badge bg-{{ $statusPulang == 'Pulang Cepat' ? 'danger' : 'success' }}">
                                    {{ $statusPulang }}
                                </span>


                            </td>
                            <td>
                                <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                                    data-bs-target="#modalDetail{{ $key }}">Detail</button>

                                <!-- Modal -->
                                <div class="modal fade" id="modalDetail{{ $key }}" tabindex="-1"
                                    aria-labelledby="modalLabel{{ $key }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="modalLabel{{ $key }}">Detail
                                                    Lokasi & Foto Selfie</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <!-- Lokasi Absen -->
                                                    <div class="col-md-6 border-end">
                                                        <h6 class="mb-3">📍 Lokasi Absen</h6>
                                                        @foreach ($item->lokasi_absen as $index => $lokasi)
                                                            <p>{{ $lokasi }}</p>
                                                            @php
                                                                $fotoAbsen = $item->file_url_absen[$index] ?? null;
                                                            @endphp
                                                            @if ($fotoAbsen)
                                                                <div class="mb-3 text-center">
                                                                    <img src="{{ asset($fotoAbsen) }}"
                                                                        class="img-fluid rounded shadow"
                                                                        style="max-height: 150px;" alt="Selfie Absen">
                                                                </div>
                                                            @else
                                                                <p class="text-muted">Foto tidak tersedia.</p>
                                                            @endif
                                                        @endforeach
                                                    </div>

                                                    <!-- Lokasi Pulang -->
                                                    <div class="col-md-6">
                                                        <h6 class="mb-3">📍 Lokasi Pulang</h6>
                                                        @foreach ($item->lokasi_pulang as $index => $lokasi)
                                                            <p>{{ $lokasi }}</p>
                                                            @php
                                                                $fotoPulang = $item->file_url_pulang[$index] ?? null;
                                                            @endphp
                                                            @if ($fotoPulang)
                                                                <div class="mb-3 text-center">
                                                                    <img src="{{ asset($fotoPulang) }}"
                                                                        class="img-fluid rounded shadow"
                                                                        style="max-height: 150px;" alt="Selfie Pulang">
                                                                </div>
                                                            @else
                                                                <p class="text-muted">Foto tidak tersedia.</p>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>


            </table>
        </div>


        <div class="mt-4">
            {{ $absensi->links() }}
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
