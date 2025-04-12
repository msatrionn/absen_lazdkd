<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function index(Request $request)
    {
        return view('login');
    }
    public function index_register(Request $request)
    {
        $jabatans = Jabatan::where('id', '!=', 1)->get();
        return view('register', compact('jabatans'));
    }
    public function register(Request $request)
    {
        $request->validate([
            // Validasi User
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:16|unique:users',
            'password' => 'required|string|min:6|confirmed',

            // Validasi Staff
            'nip' => 'required|string|unique:staff,nip|max:255',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'telp' => 'required|string|max:15',
            'id_jabatan' => 'required|exists:jabatan,id',
        ]);

        // Buat user
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        // Buat staff (relasi ke user)
        Staff::create([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'telp' => $request->telp,
            'id_jabatan' => $request->id_jabatan,
            'id_user' => $user->id,
        ]);

        Auth::login($user);

        return redirect('/');
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Coba login
        if (!Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        // Regenerate session (untuk keamanan)
        $request->session()->regenerate();

        return redirect('/');
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate(); // Hapus sesi
        $request->session()->regenerateToken(); // Regenerasi token CSRF
        return redirect('/login');
    }
}
