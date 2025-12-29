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

    // Proses Login
    public function login(Request $request) {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
        
            // Sesuaikan pengalihan dengan nilai role di migrasi
            if (Auth::user()->role === 'warehouse_admin') {
                return redirect()->intended('/admin-gudang/management');
            } else {
                return redirect()->intended('/manajer-pembelian/dashboard');
            }
        }

        return back()->withErrors(['username' => 'Username atau password salah.']);
    }

    // Menampilkan halaman register
    public function showRegister() {
        return view('auth.register');
    }

    // Proses Register
    public function register(Request $request) {
        $request->validate([
            'username' => 'required|string|max:100|unique:users',
            'real_name' => 'required|string|max:150',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:warehouse_admin,procurement_manager',
        ]);

        User::create([
            'username' => $request->username,
            'real_name' => $request->real_name,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect('/login')->with('success', 'Akun berhasil dibuat!');
    }

    // Logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}