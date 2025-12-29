<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin() {
        return view('auth.login');
    }

    //Proses Login
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Ambil data user yang baru saja login
            $user = Auth::user();

            // Pengalihan default berdasarkan role
             if ($user->role === 'admin_gudang') {
                return redirect()->intended('/admin-gudang/management');
            } elseif ($user->role === 'manajer') {
                return redirect()->intended('/manajer-pembelian/dashboard');
            }

            // Default jika role tidak dikenali
            return redirect()->intended('/');
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    // Menampilkan halaman register
    public function showRegister() {
        return view('auth.register');
    }

        // Proses Register
        public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin_gudang,manajer', // Validasi input role
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, // BARIS INI WAJIB ADA agar role tersimpan
        ]);

        return redirect('/login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }

    // Logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}