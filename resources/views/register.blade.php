<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-section-title {
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
    </style>
</head>

<body class="bg-primary d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="max-width: 900px; width: 100%;">
        <h3 class="text-center mb-4">Form Pendaftaran</h3>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="row">
                {{-- Kolom Akun --}}
                <div class="col-md-6">
                    <div class="form-section-title">Akun</div>

                    <div class="mb-3">
                        <label>Nama Akun</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}"
                            required>
                        @error('username')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                {{-- Kolom Staff --}}
                <div class="col-md-6">
                    <div class="form-section-title">Data Staff</div>

                    <div class="mb-3">
                        <label>NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" required>
                        @error('nip')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                        @error('nama')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="">Pilih</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki
                            </option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan
                            </option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" required>{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>No. Telepon</label>
                        <input type="text" name="telp" class="form-control" value="{{ old('telp') }}" required>
                        @error('telp')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>Jabatan</label>
                        <select name="id_jabatan" class="form-control" required>
                            <option value="">Pilih Jabatan</option>
                            @foreach ($jabatans as $jabatan)
                                <option value="{{ $jabatan->id }}"
                                    {{ old('id_jabatan') == $jabatan->id ? 'selected' : '' }}>
                                    {{ $jabatan->nama_jabatan }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_jabatan')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            @if (session('error'))
                <div class="alert alert-danger mt-2">{{ session('error') }}</div>
            @endif

            <div class="mt-4">
                <button type="submit" class="btn btn-primary w-100">Daftar</button>
            </div>

            {{-- <p class="text-center mt-3"><a href="{{ route('login') }}">Sudah punya akun? Login</a></p> --}}
        </form>
    </div>
</body>

</html>
