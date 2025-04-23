<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Absensi LAZDKD</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        @include('layouts.sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                @include('layouts.navbar')

                <!-- Begin Page Content -->
                <div class="container">
                    <h2 class="mb-4">Riwayat Absen</h2>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="startDate">Dari Tanggal</label>
                            <input type="date" id="startDate" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="endDate">Sampai Tanggal</label>
                            <input type="date" id="endDate" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button id="filterBtn" class="btn btn-primary">Filter</button>
                        </div>
                    </div>

                    <table class="table table-bordered" id="jabatanTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal Absen</th>
                                <th>Jadwal Absen Masuk</th>
                                <th>Absen Masuk</th>
                                <th>Lokasi Masuk</th>
                                <th>Jadwal Absen Pulang</th>
                                <th>Absen Pulang</th>
                                <th>Lokasi Pulang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>


            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        {{-- <span>Copyright &copy;</span> --}}
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    @include('layouts.logout_modal')

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <!-- Page level plugins -->
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('js/demo/datatables-demo.js') }}"></script>
    <script>
        $(document).ready(function() {
            let table = $('#jabatanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('absen.getHistoryAbsen') }}",
                    data: function(d) {
                        d.start_date = $('#startDate').val();
                        d.end_date = $('#endDate').val();
                        d.nama = $('#namaStaff').val();
                    }
                },
                language: {
                    search: "Cari Nama Karyawan:",
                    searchPlaceholder: "Ketik nama karyawan..."
                },
                columns: [{
                        data: null,
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'tgl_absen',
                        name: 'tgl_absen'
                    },
                    {
                        data: 'jam_masuk',
                        name: 'jam_masuk'
                    },
                    {
                        data: 'waktu_masuk',
                        name: 'waktu_masuk'
                    },
                    {
                        data: 'lokasi_masuk',
                        name: 'lokasi_masuk'
                    },
                    {
                        data: 'jam_keluar',
                        name: 'jam_keluar'
                    },
                    {
                        data: 'waktu_pulang',
                        name: 'waktu_pulang'
                    },
                    {
                        data: 'lokasi_pulang',
                        name: 'lokasi_pulang'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return `
                                <button class="btn btn-sm btn-primary" onclick="showDetail('${row.file_url_masuk}', '${row.file_url_pulang}')">
                                    Detail Foto
                                </button>
                            `;
                        }
                    }
                ]
            });


            $('#filterBtn').click(function() {
                table.ajax.reload();
            });
        });

        function showDetail(fileMasuk, filePulang) {
            alert(`Foto Masuk: ${fileMasuk}\nFoto Pulang: ${filePulang}`);
        }
    </script>



</body>

</html>
