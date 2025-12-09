<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    protected string $backend;

    public function __construct()
    {
        // URL backend dari config/services.php
        $this->backend = rtrim(config('services.backend.url'), '/');
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function registerProcess(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'password'     => 'required|string|min:8|confirmed',
        ]);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post($this->backend . '/api/auth/register', [
            'nama_lengkap' => $request->nama_lengkap,
            'email'        => $request->email,
            'password'     => $request->password,
        ]);

        if (! $response->successful()) {
            $json    = $response->json();
            $message = $json['message'] ?? 'Gagal membuat akun.';

            return back()
                ->withInput()
                ->withErrors(['register' => $message]);
        }

        $data  = $response->json();
        $user  = $data['user'] ?? null;
        $token = $data['authorisation']['token'] ?? null;

        if (! $user || ! $token) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Gagal login. Response server tidak valid.']);
        }

        $request->session()->regenerate();

        // === JIKA ADMIN ===
        if (!empty($user['is_admin']) && (int)$user['is_admin'] === 1) {
            session([
                'admin' => [
                    'id'    => $user['id'],
                    'nama'  => $user['nama_lengkap'],
                    'email' => $user['email'],
                ],
                'admin_token' => $token,
            ]);

            return redirect()->route('admin.dashboard');
        }

        // === USER BIASA ===
        session([
            'user' => [
                'id'    => $user['id'],
                'nama'  => $user['nama_lengkap'],
                'email' => $user['email'],
            ],
            'user_token' => $token,
        ]);

        return redirect()->intended(route('dashboard'));
    }

    public function loginForm()
    {
        return view('auth.login');
    }

    public function loginProcess(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $response = Http::post($this->backend . '/api/auth/login', [
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        if (! $response->successful()) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Email atau password salah']);
        }

        $data  = $response->json();
        $user  = $data['user'] ?? null;
        $token = $data['authorisation']['token'] ?? null;

        if (! $user || ! $token) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Gagal login. Response server tidak valid.']);
        }

        $request->session()->regenerate();

        // === JIKA ADMIN ===
        if (!empty($user['is_admin']) && (int)$user['is_admin'] === 1) {
            session([
                'admin' => [
                    'id'    => $user['id'],
                    'nama'  => $user['nama_lengkap'],
                    'email' => $user['email'],
                ],
                'admin_token' => $token,
            ]);

            return redirect()->route('admin.dashboard');
        }

        // === USER BIASA ===
        session([
            'user' => [
                'id'    => $user['id'],
                'nama'  => $user['nama_lengkap'],
                'email' => $user['email'],
            ],
            'user_token' => $token,
        ]);

        return redirect()->intended(route('dashboard'));
    }

    public function dashboard(Request $request)
    {
        // dashboard ini khusus user biasa
        if (! session()->has('user')) {
            return redirect()->route('login.form')->with('error', 'Silakan login terlebih dahulu.');
        }

        $sessionUser = session('user');
        $user        = (object) $sessionUser;

        $token = session('user_token');

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->withToken($token)->get($this->backend . '/api/laporans');

        if (! $response->successful()) {
            $request->session()->forget(['user', 'user_token']);

            return redirect()->route('login.form')
                ->with('error', 'Session berakhir, silakan login ulang.');
        }

        $laporans = collect($response->json('data') ?? []);

        $statusCounts = [
            'Baru Masuk'              => $laporans->where('status', 'Baru Masuk')->count(),
            'Sedang Diverifikasi'     => $laporans->where('status', 'Sedang Diverifikasi')->count(),
            'Selesai Ditindaklanjuti' => $laporans->where('status', 'Selesai Ditindaklanjuti')->count(),
        ];

        return view('dashboard', [
            'user'         => $user,
            'laporans'     => $laporans,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function logout(Request $request)
    {
        // bisa logout dari user atau admin, dua-duanya dibersihkan
        $userToken  = session('user_token');
        $adminToken = session('admin_token');

        try {
            if ($userToken) {
                Http::withToken($userToken)->post($this->backend . '/api/auth/logout');
            }
            if ($adminToken) {
                Http::withToken($adminToken)->post($this->backend . '/api/admin/logout');
            }
        } catch (\Throwable $e) {
            // abaikan error logout backend
        }

        $request->session()->forget(['user', 'user_token', 'admin', 'admin_token']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form')->with('status', 'Anda telah keluar.');
    }

    protected function currentUser()
    {
        $session = session('user');

        if (!is_array($session) || !isset($session['id'])) {
            return null;
        }

        return (object) [
            'id'    => $session['id'],
            'nama'  => $session['nama'],
            'email' => $session['email'],
        ];
    }
}
