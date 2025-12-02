<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function registerForm()
    {
        return view('auth.register');
    }

    public function registerProcess(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'nama_lengkap' => $data['nama_lengkap'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            DB::commit();
            $this->rememberUser($request, $user);

            return redirect()->route('dashboard');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Register failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['register' => 'Gagal membuat akun.']);
        }
    }

    /** Menampilkan form login pengguna */
    public function loginForm()
    {
        return view('login');
    }

    /** Memproses login pengguna */
    public function loginProcess(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $cred = $request->only('email', 'password');
        $user = User::where('email', $cred['email'])->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Email atau password salah'])->withInput();
        }

        $stored = $user->password;
        $passwordOk = false;

        try {
            $passwordOk = Hash::check($cred['password'], $stored);
        } catch (\RuntimeException $e) {
            $plain = $cred['password'];
            if (!$passwordOk && is_string($stored) && strlen($stored) === 32 && md5($plain) === $stored) { $passwordOk = true; }
            if (!$passwordOk && is_string($stored) && strlen($stored) === 40 && sha1($plain) === $stored) { $passwordOk = true; }
            if (!$passwordOk && $plain === $stored) { $passwordOk = true; }
        }

        if (! $passwordOk) {
            return back()->withErrors(['email' => 'Email atau password salah'])->withInput();
        }

        try {
            if (Hash::needsRehash($stored) || !is_string($stored) || strpos($stored, '$2y$') !== 0) {
                $user->password = Hash::make($cred['password']);
                $user->save();
            }
        } catch (\Throwable $e) {
            // abaikan error rehash
        }

        $this->rememberUser($request, $user);
        return redirect()->intended(route('dashboard'));
    }

    public function dashboard(Request $request)
    {
        if (! session()->has('user')) {
            return redirect()->route('login.form')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = $this->currentUser();
        if (! $user) {
            $request->session()->forget('user');
            return redirect()->route('login.form')->with('error', 'Session tidak valid. Silakan login ulang.');
        }

        $laporans = Laporan::with('pelapor')
            ->where('pelapor_id', $user->id)
            ->latest()
            ->get();

        $statusCounts = [
            'Baru Masuk' => $laporans->where('status', 'Baru Masuk')->count(),
            'Sedang Diverifikasi' => $laporans->where('status', 'Sedang Diverifikasi')->count(),
            'Selesai Ditindaklanjuti' => $laporans->where('status', 'Selesai Ditindaklanjuti')->count(),
        ];

        return view('dashboard', [
            'user' => $user,
            'laporans' => $laporans,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form')->with('status', 'Anda telah keluar.');
    }

    protected function rememberUser(Request $request, User $user): void
    {
        $request->session()->regenerate();
        session([
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama_lengkap,
                'email' => $user->email,
            ],
        ]);
    }

    protected function currentUser(): ?User
    {
        $session = session('user');
        if (! is_array($session) || ! isset($session['id'])) {
            return null;
        }

        return User::find($session['id']);
    }
}
