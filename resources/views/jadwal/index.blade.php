<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SB Admin 2 - Dashboard</title>

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
    <style>
        table.dataTable {
            width: 100% !important;
        }
    </style>
    <!-- Page Wrapper -->
    <div id="wrapper">
        @include('layouts.sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                @include('layouts.navbar')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <h2 class="mb-4">Data Jadwal</h2>
                    <button class="btn btn-primary mb-3" id="btn-add">Tambah Jadwal</button>
                    <table class="table table-bordered" id="jadwalTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="jadwalModal" tabindex="-1" aria-labelledby="jadwalModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="jadwalModalLabel">Tambah Jadwal</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="jadwalForm">
                                <div class="modal-body">
                                    <input type="hidden" id="jadwal_id">
                                    <div class="form-group">
                                        <label for="jam_masuk">Jam Masuk</label>
                                        <input type="time" id="jam_masuk" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="jam_masuk">Jam Keluar</label>
                                        <input type="time" id="jam_keluar" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>User</label>
                                        <select id="user" class="form-control" required>
                                        </select>
                                        <small class="text-danger" id="error-id_user"></small>
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
            let table = $('#jadwalTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('jadwal.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'jam_masuk',
                        name: 'jam_masuk'
                    },
                    {
                        data: 'jam_keluar',
                        name: 'jam_keluar'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            $.get("{{ route('user.list') }}", function(data) {
                let options = '';
                data.forEach(user => {
                    options += `<option value="${user.id}">${user.nama}</option>`;
                });
                $('#user').html(options);
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#btn-add').click(function() {
                $('#jadwalForm')[0].reset();
                $('#jadwal_id').val('');
                $('#jadwalModalLabel').text('Tambah Jadwal');
                $('.text-danger').text('');
                $('#jadwalModal').modal('show');
            });

            $('#jadwalTable').on('click', '.edit', function() {
                let id = $(this).data('id');
                $('.text-danger').text('');
                $.get("{{ url('/dashboard/jadwal') }}/" + id + "/edit", function(data) {
                    $('#jadwal_id').val(data.id);
                    $('#jam_masuk').val(data.jam_masuk);
                    $('#jam_keluar').val(data.jam_keluar);
                    $('#jadwalModalLabel').text('Edit Jadwal');
                    $('#jadwalModal').modal('show');
                });
            });

            $('#jadwalForm').submit(function(e) {
                e.preventDefault();
                let id = $('#jadwal_id').val();
                let url = id ? "{{ url('/dashboard/jadwal') }}/" + id : "{{ route('jadwal.store') }}";
                let method = id ? 'PUT' : 'POST';

                let formData = {
                    jam_masuk: $('#jam_masuk').val(),
                    jam_keluar: $('#jam_keluar').val(),
                    _token: "{{ csrf_token() }}"
                };

                if (!id) {
                    formData.id_user = $('#user').val(); // hanya dikirim saat tambah
                }

                $.ajax({
                    url: url,
                    type: method,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    success: function(response) {
                        $('#jadwalModal').modal('hide');
                        table.ajax.reload();
                        alert(response.success);
                    },
                    error: function(xhr) {
                        $('#error-jam_masuk').text('');
                        $('#error-jam_keluar').text('');
                        $('#error-id_user').text('');

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.jam_masuk) {
                                $('#error-jam_masuk').text(errors.jam_masuk[0]);
                            }
                            if (errors.jam_keluar) {
                                $('#error-jam_keluar').text(errors.jam_keluar[0]);
                            }
                            if (errors.id_user) {
                                $('#error-id_user').text(errors.id_user[0]);
                            }
                        } else {
                            alert('Terjadi kesalahan, silakan coba lagi.');
                        }
                    }
                });

            });

            $('#jadwalTable').on('click', '.edit', function() {
                let id = $(this).data('id');
                $('.text-danger').text('');
                $.get("{{ url('/dashboard/jadwal') }}/" + id + "/edit", function(data) {
                    $('#jadwal_id').val(data.id);
                    $('#jam_masuk').val(data.jam_masuk);
                    $('#jam_keluar').val(data.jam_keluar);
                    $('#user').val(data.id_user).prop('disabled', true);
                    $('#jadwalModalLabel').text('Edit Jadwal');
                    $('#jadwalModal').modal('show');
                });
            });

            $('#btn-add').click(function() {
                $('#user').prop('disabled', false);
            });

        });
    </script>

</body>

</html>
