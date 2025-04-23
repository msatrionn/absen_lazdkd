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
                    <h2 class="mb-4">Data Jabatan</h2>
                    <button class="btn btn-primary mb-3" id="btn-add">Tambah Jabatan</button>
                    <table class="table table-bordered" id="jabatanTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Jabatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="jabatanModal" tabindex="-1" aria-labelledby="jabatanModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="jabatanModalLabel">Tambah Jabatan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="jabatanForm">
                                <div class="modal-body">
                                    <input type="hidden" id="jabatan_id">
                                    <div class="form-group">
                                        <label for="nama_jabatan">Nama Jabatan</label>
                                        <input type="text" id="nama_jabatan" class="form-control" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->

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
                ajax: "{{ route('jabatan.data') }}",
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
                        data: 'nama_jabatan',
                        name: 'nama_jabatan'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            console.log($('meta[name="csrf-token"]').attr('content'));

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#btn-add').click(function() {
                $('#jabatanForm')[0].reset();
                $('#jabatan_id').val('');
                $('#jabatanModalLabel').text('Tambah Jabatan');
                $('#jabatanModal').modal('show');
            });

            $('#jabatanTable').on('click', '.edit', function() {
                let id = $(this).data('id');
                $.get("{{ url('/dashboard/jabatan') }}/" + id + "/edit", function(data) {
                    $('#jabatan_id').val(data.id);
                    $('#nama_jabatan').val(data.nama_jabatan);
                    $('#jabatanModalLabel').text('Edit Jabatan');
                    $('#jabatanModal').modal('show');
                });
            });

            $('#jabatanForm').submit(function(e) {
                e.preventDefault();
                let id = $('#jabatan_id').val();
                let url = id ? "{{ url('/dashboard/jabatan') }}/" + id : "{{ route('jabatan.store') }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        nama_jabatan: $('#nama_jabatan').val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#jabatanModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.success,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan, silakan coba lagi.');
                    }
                });
            });

            $('#jabatanTable').on('click', '.delete', function() {
                if (confirm('Yakin ingin menghapus jabatan ini?')) {
                    let id = $(this).data('id');
                    $.ajax({
                        url: "{{ url('/dashboard/jabatan') }}/" + id,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            table.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.success,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            alert('Terjadi kesalahan, silakan coba lagi.');
                        }
                    });
                }
            });
        });
    </script>

</body>

</html>
