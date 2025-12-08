<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LaporanController extends Controller
{
    protected string $backend;

    public function __construct()
    {
        // URL backend dari config/services.php
        $this->backend = rtrim(config('services.backend.url'), '/');
    }

    /**
     * Form buat laporan baru (GET /lapor)
     */
    public function create()
    {
        if (! session()->has('user')) {
            return redirect()->route('login.form');
        }

        return view('lapor.form', [
            'laporan' => null,
            'isEdit'  => false,
        ]);
    }

    /**
     * Simpan laporan baru (POST /lapor)
     */
    public function store(Request $request)
    {
        if (! session()->has('user')) {
            return redirect()->route('login.form');
        }

        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori'  => 'required|in:Keamanan,Aksesibilitas,Fasilitas Rusak',
            'lokasi'    => 'required|string|max:255',
            'foto'      => 'required|image|max:2048',
        ]);

        $token = session('user_token');

        $http = Http::withToken($token)->acceptJson();

        // kirim sebagai multipart (ada file)
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');

            $http = $http->attach(
                'foto',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            );
        }

        $response = $http->post($this->backend . '/api/laporans', [
            'judul'     => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kategori'  => $request->kategori,
            'lokasi'    => $request->lokasi,
        ]);

        if (! $response->successful()) {
            $msg = $response->json('message') ?? 'Gagal mengirim laporan.';
            return back()->withInput()->withErrors(['lapor' => $msg]);
        }

        return redirect()
            ->route('dashboard')
            ->with('status', 'Laporan berhasil dikirim.');
    }

    /**
     * Detail laporan (GET /laporan/{id})
     */
    public function show($id)
    {
        if (! session()->has('user')) {
            return redirect()->route('login.form');
        }

        $token = session('user_token');

        $response = Http::withToken($token)
            ->acceptJson()
            ->get($this->backend . "/api/laporans/{$id}");

        if ($response->status() === 404) {
            abort(404);
        }

        if (! $response->successful()) {
            abort(500, 'Gagal mengambil data laporan');
        }

        $laporan = $response->json('data') ?? [];

        return view('laporan.show', [
            'laporan' => $laporan,
        ]);
    }

    /**
     * Form edit laporan (GET /laporan/{id}/edit)
     */
    public function editUser($id)
    {
        if (! session()->has('user')) {
            return redirect()->route('login.form');
        }

        $token = session('user_token');

        $response = Http::withToken($token)
            ->acceptJson()
            ->get($this->backend . "/api/laporans/{$id}");

        if ($response->status() === 404) {
            abort(404);
        }

        if (! $response->successful()) {
            abort(500, 'Gagal mengambil data laporan');
        }

        $laporan = (object) ($response->json('data') ?? []);

        return view('lapor.form', [
            'laporan' => $laporan,
            'isEdit'  => true,
        ]);
    }

    /**
     * Update laporan (PUT /laporan/{id})
     */
    public function updateUser(Request $request, $id)
    {
        if (! session()->has('user')) {
            return redirect()->route('login.form');
        }

        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori'  => 'required|in:Keamanan,Aksesibilitas,Fasilitas Rusak',
            'lokasi'    => 'required|string|max:255',
            'foto'      => 'nullable|image|max:2048',
        ]);

        $token = session('user_token');

        $http = Http::withToken($token)->acceptJson();

        // kalau ada foto baru, kirim sebagai multipart + spoof _method=PUT
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');

            $http = $http->attach(
                'foto',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            );
        }

        $payload = [
            '_method'   => 'PUT', // supaya sampai di backend ke method update()
            'judul'     => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kategori'  => $request->kategori,
            'lokasi'    => $request->lokasi,
        ];

        $response = $http->post($this->backend . "/api/laporans/{$id}", $payload);

        if (! $response->successful()) {
            $msg = $response->json('message') ?? 'Gagal mengupdate laporan.';
            return back()->withInput()->withErrors(['lapor' => $msg]);
        }

        return redirect()
            ->route('dashboard')
            ->with('status', 'Laporan berhasil diperbarui.');
    }

    /**
     * Hapus laporan (DELETE /laporan/{id})
     */
    public function destroyUser($id)
    {
        if (! session()->has('user')) {
            return redirect()->route('login.form');
        }

        $token = session('user_token');

        $response = Http::withToken($token)
            ->acceptJson()
            ->delete($this->backend . "/api/laporans/{$id}");

        if (! $response->successful()) {
            $msg = $response->json('message') ?? 'Gagal menghapus laporan.';
            return back()->withErrors(['lapor' => $msg]);
        }

        return redirect()
            ->route('dashboard')
            ->with('status', 'Laporan berhasil dihapus.');
    }
}
