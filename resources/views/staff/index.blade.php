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
                                <th>NO</th>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah/Edit Staff</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="staffForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="hidden" id="id_staff" name="id_staff">

                                    <label>Username</label>
                                    <input type="text" id="username" name="username"
                                        class="form-control @error('username') is-invalid @enderror">
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group password-fields">
                                    <label>Password</label>
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group password-fields">
                                    <label>Konfirmasi Password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>NIP</label>
                                    <input type="text" id="nip" name="nip"
                                        class="form-control @error('nip') is-invalid @enderror">
                                    @error('nip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" id="nama" name="nama"
                                        class="form-control @error('nama') is-invalid @enderror">
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <select id="jenis_kelamin" name="jenis_kelamin"
                                        class="form-control @error('jenis_kelamin') is-invalid @enderror">
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror"></textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Telepon</label>
                                    <input type="text" id="telp" name="telp"
                                        class="form-control @error('telp') is-invalid @enderror">
                                    @error('telp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Jabatan</label>
                                    <select id="jabatan" name="jabatan"
                                        class="form-control @error('jabatan') is-invalid @enderror">
                                    </select>
                                    @error('jabatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group" id="changePasswordBtnContainer" style="display: none;">
                                    <button type="button" class="btn btn-warning" id="changePasswordBtn">
                                        <i class="fas fa-key"></i> Ganti Password
                                    </button>
                                </div>
                            </div>
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

    <!-- Modal Change Password -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ganti Password</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="changePasswordForm">
                    <div class="modal-body">
                        <input type="hidden" id="password_staff_id" name="id_staff">
                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" id="new_password" name="new_password"
                                class="form-control @error('new_password') is-invalid @enderror">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                class="form-control @error('new_password_confirmation') is-invalid @enderror">
                            @error('new_password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Password</button>
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
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
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
                        data: null,
                        name: 'no',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
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

            $('#staffTable').on('click', '.edit', function() {
                let id = $(this).data('id');
                $.get("{{ url('/dashboard/staff') }}/" + id + "/edit", function(response) {
                    $('#id_staff').val(response.id);
                    $('#username').val(response.username ?? '').prop('disabled', true);
                    $('#nip').val(response.nip).prop('readonly', true);
                    $('#nama').val(response.nama);
                    $('#jenis_kelamin').val(response.jenis_kelamin);
                    $('#alamat').val(response.alamat);
                    $('#telp').val(response.telp);
                    $('#jabatan').val(response.id_jabatan);

                    // Hide password fields and show change password button
                    $('.password-fields').hide();
                    $('#changePasswordBtnContainer').show();

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
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.success,
                            timer: 2000,
                            showConfirmButton: false
                        });
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
                    // Remove password fields from data when editing
                    delete data.password;
                    delete data.password_confirmation;
                }

                // Kirim data ke server
                $.ajax({
                    url: url,
                    type: method,
                    data: data, // Gunakan objek data yang sudah disiapkan
                    success: function(response) {
                        $('#staffModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.success,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            let errors = response.responseJSON.errors;
                            // Reset semua error
                            $('#staffForm .is-invalid').removeClass('is-invalid');
                            $('#staffForm .invalid-feedback').remove();

                            // Loop setiap error dan tampilkan di input terkait
                            $.each(errors, function(field, message) {
                                let input = $('[name="' + field + '"]');
                                input.addClass('is-invalid');
                                if (input.next('.invalid-feedback').length === 0) {
                                    input.after('<div class="invalid-feedback">' +
                                        message[0] + '</div>');
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    }
                });
            });

            // Event listener for add button
            $('#btn-add').click(function() {
                $('#staffForm')[0].reset();
                $('#id_staff').val('');
                $('#nip').prop('readonly', false);
                $('#username').prop('disabled', false);
                // Show password fields for new staff
                $('.password-fields').show();
                $('#changePasswordBtnContainer').hide();
                $('#staffForm .is-invalid').removeClass('is-invalid');
                $('#staffForm .invalid-feedback').remove();
                $('#staffModal').modal('show');
            });

            // Event listener for change password button
            $('#changePasswordBtn').click(function() {
                $('#password_staff_id').val($('#id_staff').val());
                $('#changePasswordModal').modal('show');
            });

            // Event listener for change password form
            $('#changePasswordForm').submit(function(e) {
                e.preventDefault();

                let staffId = $('#password_staff_id').val();
                let data = {
                    new_password: $('#new_password').val(),
                    new_password_confirmation: $('#new_password_confirmation').val()
                };

                $.ajax({
                    url: "{{ url('/dashboard/change-password') }}/" + staffId,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        $('#changePasswordModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.success,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#changePasswordForm')[0].reset();
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            let errors = response.responseJSON.errors;
                            // Reset semua error
                            $('#changePasswordForm .is-invalid').removeClass('is-invalid');
                            $('#changePasswordForm .invalid-feedback').remove();

                            // Loop setiap error dan tampilkan di input terkait
                            $.each(errors, function(field, message) {
                                let input = $('[name="' + field.replace('new_', '') +
                                    '"]');
                                input.addClass('is-invalid');
                                if (input.next('.invalid-feedback').length === 0) {
                                    input.after('<div class="invalid-feedback">' +
                                        message[0] + '</div>');
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>
