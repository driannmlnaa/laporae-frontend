<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(): RedirectResponse
    {
        if (session()->has('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    public function create(): View|RedirectResponse
    {
        if ($redirect = $this->ensureUserAuthenticated()) {
            return $redirect;
        }

        return view('laporan.create', [
            'user' => session('user'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($redirect = $this->ensureUserAuthenticated()) {
            return $redirect;
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'kategori' => ['required', Rule::in(Laporan::KATEGORI_OPTIONS)],
            'lokasi' => ['required', 'string', 'max:255'],
            'foto' => ['required', 'image', 'max:2048'],
        ]);

        $user = session('user');

        $fotoPath = $this->storeFoto($request->file('foto'));

        Laporan::create([
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'kategori' => $validated['kategori'],
            'lokasi' => $validated['lokasi'],
            'foto' => $fotoPath,
            'status' => 'Baru Masuk',
            'pelapor_id' => $user['id'] ?? null,
        ]);

        return redirect()->route('dashboard')->with('status', 'Laporan berhasil dikirim.');
    }

    public function show(int $id): View|RedirectResponse
    {
        if ($redirect = $this->ensureUserAuthenticated()) {
            return $redirect;
        }

        $laporan = Laporan::with('pelapor')->findOrFail($id);

        $user = session('user');
        if (! is_array($user) || ($laporan->pelapor_id !== ($user['id'] ?? null))) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        return view('laporan.show', [
            'laporan' => $laporan,
        ]);
    }

    public function edit(int $id): View|RedirectResponse
    {
        if ($redirect = $this->ensureAdminAuthenticated()) {
            return $redirect;
        }

        $laporan = Laporan::findOrFail($id);

        return view('admin.laporan.edit', [
            'laporan' => $laporan,
            'admin' => session('admin'),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($redirect = $this->ensureAdminAuthenticated()) {
            return $redirect;
        }

        $validated = $request->validate([
            'status' => ['required', 'in:Baru Masuk,Sedang Diverifikasi,Selesai Ditindaklanjuti'],
        ]);

        $laporan = Laporan::findOrFail($id);
        $laporan->update([
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.dashboard')->with('status', 'Status laporan diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($redirect = $this->ensureAdminAuthenticated()) {
            return $redirect;
        }

        $laporan = Laporan::findOrFail($id);
        $this->deleteFoto($laporan->foto);
        $laporan->delete();

        return redirect()->route('admin.dashboard')->with('status', 'Laporan berhasil dihapus.');
    }

    public function editUser(int $id): View|RedirectResponse
    {
        if ($redirect = $this->ensureUserAuthenticated()) {
            return $redirect;
        }

        $laporan = Laporan::findOrFail($id);
        $this->authorizeUserOwnership($laporan);

        return view('laporan.edit', [
            'laporan' => $laporan,
        ]);
    }

    public function updateUser(Request $request, int $id): RedirectResponse
    {
        if ($redirect = $this->ensureUserAuthenticated()) {
            return $redirect;
        }

        $laporan = Laporan::findOrFail($id);
        $this->authorizeUserOwnership($laporan);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'kategori' => ['required', Rule::in(Laporan::KATEGORI_OPTIONS)],
            'lokasi' => ['required', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $this->deleteFoto($laporan->foto);
            $validated['foto'] = $this->storeFoto($request->file('foto'));
        }

        $laporan->update($validated);

        return redirect()->route('laporan.show', $laporan->id)->with('status', 'Laporan berhasil diperbarui.');
    }

    public function destroyUser(int $id): RedirectResponse
    {
        if ($redirect = $this->ensureUserAuthenticated()) {
            return $redirect;
        }

        $laporan = Laporan::findOrFail($id);
        $this->authorizeUserOwnership($laporan);

        $this->deleteFoto($laporan->foto);
        $laporan->delete();

        return redirect()->route('dashboard')->with('status', 'Laporan berhasil dihapus.');
    }

    protected function ensureUserAuthenticated(): ?RedirectResponse
    {
        if (! session()->has('user')) {
            return redirect()->route('login.form')->with('error', 'Silakan login sebagai pengguna.');
        }

        $user = session('user');
        if (! is_array($user) || ! isset($user['id'])) {
            session()->forget('user');
            return redirect()->route('login.form')->with('error', 'Session pengguna tidak valid.');
        }

        return null;
    }

    protected function ensureAdminAuthenticated(): ?RedirectResponse
    {
        if (! session()->has('admin')) {
            return redirect()->route('admin.login.form')->with('error', 'Silakan login sebagai admin.');
        }

        return null;
    }

    protected function authorizeUserOwnership(Laporan $laporan): void
    {
        $user = session('user');
        if (! is_array($user) || ($laporan->pelapor_id !== ($user['id'] ?? null))) {
            abort(403, 'Anda tidak berhak mengakses laporan ini.');
        }
    }

    protected function storeFoto($file): string
    {
        $directory = 'uploads/laporans';
        File::ensureDirectoryExists(public_path($directory));

        $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $filename);

        return $directory . '/' . $filename;
    }

    protected function deleteFoto(?string $path): void
    {
        if (! $path) {
            return;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        $publicPath = public_path($path);
        if (File::exists($publicPath)) {
            File::delete($publicPath);
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
