<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100 bg-primary">
    <div class="card p-4 shadow" style="width: 350px;">
        <h3 class="text-center">Login</h3>
        <form action="{{ route('login') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            @if (session('errors'))
                <div class="alert alert-danger">
                    <ul>
                        @foreach (session('errors')->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{-- @if (Auth::check())
                <p>Halo, {{ Auth::user()->username }}</p>
                <p>Email: {{ Auth::user()->email }}</p>
            @else
                <p>Silakan <a href="{{ route('login') }}">login</a> terlebih dahulu.</p>
            @endif --}}
            <button type="submit" class="btn btn-primary w-100">Login</button>
            {{-- <p class="text-center mt-3"><a href="{{ route('register') }}">Belum punya akun? Register</a></p> --}}
        </form>
    </div>
</body>

</html>
