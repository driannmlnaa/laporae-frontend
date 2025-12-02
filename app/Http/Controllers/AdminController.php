<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function loginForm(): View
    {
        return view('admin.auth.login');
    }

    public function loginProcess(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::where('email', $request->input('email'))->first();

        if (! $admin || ! Hash::check($request->input('password'), $admin->password)) {
            return back()->withInput()->withErrors(['email' => 'Kredensial tidak valid.']);
        }

        $request->session()->regenerate();

        session(['admin' => [
            'id' => $admin->id,
            'nama' => $admin->nama,
            'email' => $admin->email,
        ]]);

        return redirect()->route('admin.dashboard')->with('status', 'Login admin berhasil.');
    }

    public function index(): View|RedirectResponse
    {
        if ($redirect = $this->ensureAdminAuthenticated()) {
            return $redirect;
        }

        return view('admin.dashboard', [
            'laporans' => Laporan::latest()->get(),
            'admin' => session('admin'),
        ]);
    }

    public function edit(int $id): View|RedirectResponse
    {
        if ($redirect = $this->ensureAdminAuthenticated()) {
            return $redirect;
        }

        return app(LaporanController::class)->edit($id);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($redirect = $this->ensureAdminAuthenticated()) {
            return $redirect;
        }

        return app(LaporanController::class)->update($request, $id);
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($redirect = $this->ensureAdminAuthenticated()) {
            return $redirect;
        }

        return app(LaporanController::class)->destroy($id);
    }

    protected function ensureAdminAuthenticated(): ?RedirectResponse
    {
        if (! session()->has('admin')) {
            return redirect()->route('admin.login.form')->with('error', 'Silakan login sebagai admin.');
        }

        return null;
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form')->with('status', 'Logout admin berhasil.');
    }
}
