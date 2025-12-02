<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Buat Akun - {{ config('app.name', 'LaporAE') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-7">
                <div class="text-center mb-4">
                    <h1 class="h4 mb-1">Registrasi Pengguna</h1>
                    <p class="text-muted mb-0">Buat akun untuk mulai melaporkan dan memantau tindak lanjut laporan.</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 text-center mb-3">Buat Akun Baru</h2>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register.process') }}" class="mt-3">
                            @csrf

                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input id="nama_lengkap" type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="form-control @error('nama_lengkap') is-invalid @enderror" placeholder="Nama lengkap sesuai identitas" required autofocus>
                                @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password minimal 8 karakter" required>
                                <div class="form-text">Gunakan kombinasi huruf dan angka minimal 8 karakter.</div>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" type="submit">Buat Akun</button>
                                <a class="btn btn-outline-secondary" href="{{ route('login.form') }}">Sudah punya akun? Masuk</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
