<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    private string $backend;

    public function __construct()
    {
        $this->backend = rtrim(config('services.backend.url'), '/');
    }

    public function loginForm()
    {
        // resources/views/admin/login.blade.php
        return view('admin.login');
    }

    public function loginProcess(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $response = Http::post($this->backend . '/api/admin/login', [
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
                ->withErrors(['email' => 'Gagal login admin.']);
        }

        $request->session()->regenerate();

        session([
            'admin' => [
                'id'    => $user['id'],
                'nama'  => $user['nama'] ?? ($user['nama_lengkap'] ?? $user['email']),
                'email' => $user['email'],
            ],
            'admin_token' => $token,
        ]);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $token = session('admin_token');
        if ($token) {
            try {
                Http::withToken($token)->post($this->backend . '/api/admin/logout');
            } catch (\Throwable $e) {
                // diamkan saja
            }
        }

        $request->session()->forget(['admin', 'admin_token']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Anda telah keluar sebagai admin.');
    }

    protected function ensureAdmin()
    {
        if (! session()->has('admin')) {
            return redirect()->route('admin.login.form');
        }

        return (object) session('admin');
    }


    public function index(Request $request)
    {
        $admin = $this->ensureAdmin();
        if ($admin instanceof \Illuminate\Http\RedirectResponse) {
            return $admin;
        }

        $token = session('admin_token');

        $response = Http::withToken($token)
            ->acceptJson()
            ->get($this->backend . '/api/admin/laporans');

        if (! $response->successful()) {
            return back()->withErrors([
                'admin' => 'Gagal mengambil data laporan dari backend.',
            ]);
        }

        $laporans = collect($response->json('laporans') ?? []);

        return view('admin.dashboard', [
            'admin'    => $admin,
            'laporans' => $laporans,
        ]);
    }

    public function edit($id)
    {
        $admin = $this->ensureAdmin();
        if ($admin instanceof \Illuminate\Http\RedirectResponse) {
            return $admin;
        }

        $token = session('admin_token');

        $response = Http::withToken($token)
            ->acceptJson()
            ->get($this->backend . "/api/admin/laporans/{$id}");

        if ($response->status() === 404) {
            abort(404);
        }
        if (! $response->successful()) {
            abort(500, 'Gagal mengambil data laporan.');
        }

        $laporan = (object) ($response->json('laporan') ?? []);

        return view('admin.laporan_edit', [
            'admin'   => $admin,
            'laporan' => $laporan,
        ]);
    }

    public function update(Request $request, $id)
    {
        $admin = $this->ensureAdmin();
        if ($admin instanceof \Illuminate\Http\RedirectResponse) {
            return $admin;
        }

        $request->validate([
            'status' => 'required|in:Baru Masuk,Sedang Diverifikasi,Selesai Ditindaklanjuti',
        ]);

        $token = session('admin_token');

        $response = Http::withToken($token)
            ->acceptJson()
            ->put($this->backend . "/api/admin/laporans/{$id}", [
                'status' => $request->status,
            ]);

        if (! $response->successful()) {
            $msg = $response->json('message') ?? 'Gagal mengubah status laporan.';
            return back()->withInput()->withErrors(['status' => $msg]);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Status laporan berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $admin = $this->ensureAdmin();
        if ($admin instanceof \Illuminate\Http\RedirectResponse) {
            return $admin;
        }

        $token = session('admin_token');

        $response = Http::withToken($token)
            ->acceptJson()
            ->delete($this->backend . "/api/admin/laporans/{$id}");

        if (! $response->successful()) {
            $msg = $response->json('message') ?? 'Gagal menghapus laporan.';
            return back()->withErrors(['hapus' => $msg]);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Laporan berhasil dihapus.');
    }
}
