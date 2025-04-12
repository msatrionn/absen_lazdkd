<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Staff</title>

    <!-- CSS -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        @include('layouts.sidebar')
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('layouts.navbar')
                <div class="container">
                    <h2 class="mb-4">Data Staff</h2>
                    <button class="btn btn-primary mb-3" id="btn-add">Tambah Staff</button>
                    <table class="table table-bordered" id="staffTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Jenis Kelamin</th>
                                <th>Alamat</th>
                                <th>Telepon</th>
                                <th>Jabatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit/Create -->
    <div class="modal fade" id="staffModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah/Edit Staff</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="staffForm">
                    <div class="modal-body">
                        <input type="hidden" id="id_staff" name="id_staff">
                        <div class="form-group">
                            <label>NIP</label>
                            <input type="text" id="nip" name="nip" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" id="nama" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select id="jenis_kelamin" name="jenis_kelamin" class="form-control">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea id="alamat" name="alamat" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Telepon</label>
                            <input type="text" id="telp" name="telp" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Jabatan</label>
                            <select id="jabatan" class="form-control" required>
                            </select>
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

    <!-- Modal Delete -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus staff ini?</p>
                    <input type="hidden" id="delete_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Include CSRF token in AJAX headers
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let table = $('#staffTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('staff.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'nip',
                        name: 'nip'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'jenis_kelamin',
                        name: 'jenis_kelamin'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'telp',
                        name: 'telp'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Event listener for edit button
            $('#staffTable').on('click', '.edit', function() {
                let id = $(this).data('id');
                $.get("{{ url('/dashboard/staff') }}/" + id + "/edit", function(response) {
                    $('#id_staff').val(response.id);
                    $('#nip').val(response.nip).prop('readonly', true); // Set readonly untuk nip
                    $('#nama').val(response.nama);
                    $('#jenis_kelamin').val(response.jenis_kelamin);
                    $('#alamat').val(response.alamat);
                    $('#telp').val(response.telp);
                    $('#jabatan').val(response.id_jabatan); // Set nilai jabatan
                    $('#staffModal').modal('show');
                });
            });

            // Load jabatan list
            $.get("{{ route('jabatan.list') }}", function(data) {
                let options = '';
                data.forEach(jabatan => {
                    options += `<option value="${jabatan.id}">${jabatan.nama_jabatan}</option>`;
                });
                $('#jabatan').html(options);
            });

            // Event listener for delete button
            $('#staffTable').on('click', '.delete', function() {
                let id = $(this).data('id');
                $('#delete_id').val(id);
                $('#deleteModal').modal('show');
            });

            // Event listener for delete confirmation
            $('#confirmDelete').click(function() {
                let id = $('#delete_id').val();
                $.ajax({
                    url: "{{ url('/dashboard/staff') }}/" + id,
                    type: 'DELETE',
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();
                        alert(response.success);
                    }
                });
            });

            // Event listener for form submission
            $('#staffForm').submit(function(e) {
                e.preventDefault();

                // Ambil nilai jabatan_id dari dropdown
                let jabatanId = $('#jabatan').val();

                // Buat objek FormData
                let formData = $(this).serializeArray(); // Serialize form data ke array
                formData.push({
                    name: 'id_jabatan',
                    value: jabatanId
                }); // Tambahkan jabatan_id ke formData

                // Konversi array ke object
                let data = {};
                $.each(formData, function(index, field) {
                    data[field.name] = field.value;
                });

                // Tentukan URL dan method berdasarkan id_staff
                let staffId = $('#id_staff').val();
                let url = staffId ? "{{ url('/dashboard/staff') }}/" + staffId :
                    "{{ route('staff.store') }}";
                let method = staffId ? 'PUT' : 'POST';
                if (staffId) {
                    delete data.nip;
                    delete data.id_staff;
                }

                // Kirim data ke server
                $.ajax({
                    url: url,
                    type: method,
                    data: data, // Gunakan objek data yang sudah disiapkan
                    success: function(response) {
                        $('#staffModal').modal('hide');
                        table.ajax.reload();
                        alert(response.success);
                    },
                    error: function(response) {
                        alert('An error occurred. Please try again.');
                    }
                });
            });

            // Event listener for add button
            $('#btn-add').click(function() {
                $('#staffForm')[0].reset(); // Reset form
                $('#id_staff').val(''); // Clear ID
                $('#nip').prop('readonly', false); // Hapus readonly untuk nip
                $('#staffModal').modal('show');
            });
        });
    </script>
</body>

</html>
