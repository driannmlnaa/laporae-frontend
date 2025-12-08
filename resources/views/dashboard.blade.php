@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name', 'LaporAE'))

@section('content')
@php
    $displayName = $user->nama ?? $user->nama_lengkap ?? $user->email ?? 'Pengguna';
    $statusCounts = $statusCounts ?? [
        'Baru Masuk' => 0,
        'Sedang Diverifikasi' => 0,
        'Selesai Ditindaklanjuti' => 0,
    ];
@endphp

<div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Dashboard Pengguna</h1>
        <p class="text-muted mb-1">
            Selamat datang, <span class="fw-semibold">{{ $displayName }}</span>. 
            Pantau status laporan Anda di aplikasi <span class="fw-semibold text-primary">LaporAe</span>.
        </p>
        <small class="text-muted">
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </small>
    </div>
    <a href="{{ route('lapor.create') }}" class="btn btn-primary align-self-center shadow-sm">
        + Buat Laporan
    </a>
</div>


<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-uppercase text-muted small mb-1">Baru Masuk</p>
                <h2 class="fw-semibold mb-2">{{ $statusCounts['Baru Masuk'] }}</h2>
                <span class="badge bg-primary-subtle text-primary">Menunggu verifikasi</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-uppercase text-muted small mb-1">Sedang Diverifikasi</p>
                <h2 class="fw-semibold mb-2">{{ $statusCounts['Sedang Diverifikasi'] }}</h2>
                <span class="badge bg-warning-subtle text-warning-emphasis">Diproses petugas</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-uppercase text-muted small mb-1">Selesai Ditindaklanjuti</p>
                <h2 class="fw-semibold mb-2">{{ $statusCounts['Selesai Ditindaklanjuti'] }}</h2>
                <span class="badge bg-success-subtle text-success-emphasis">Tindak lanjut selesai</span>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h5 class="mb-1">Daftar Laporan Saya</h5>
        <p class="text-muted mb-0">Semua laporan ditampilkan dari terbaru.</p>
    </div>
    <div class="card-body">
        @if ($laporans->isEmpty())
            <p class="text-muted mb-0">Belum ada laporan. Yuk buat laporan pertama Anda!</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Dokumentasi</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporans as $raw)
                            @php
                                // pastikan berbentuk object
                                $laporan = is_array($raw) ? (object) $raw : $raw;

                                $status = $laporan->status ?? '';

                                $badgeClass = match ($status) {
                                    'Baru Masuk'              => 'bg-primary-subtle text-primary',
                                    'Sedang Diverifikasi'     => 'bg-warning-subtle text-warning-emphasis',
                                    'Selesai Ditindaklanjuti' => 'bg-success-subtle text-success-emphasis',
                                    default                   => 'bg-secondary-subtle text-secondary-emphasis',
                                };

                                // foto dari backend: field-nya "foto" → jadikan URL penuh
                                $fotoPath = $laporan->foto ?? null;
                                $fotoUrl  = $fotoPath ? url($fotoPath) : null;

                                // created_at dari API biasanya string
                                $createdAt = null;
                                if (!empty($laporan->created_at)) {
                                    try {
                                        $createdAt = \Carbon\Carbon::parse($laporan->created_at)->format('d M Y H:i');
                                    } catch (\Throwable $e) {
                                        $createdAt = $laporan->created_at;
                                    }
                                }
                            @endphp

                            <tr>
                                <td>{{ $laporan->judul ?? '-' }}</td>
                                <td>{{ $laporan->kategori ?? '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ $badgeClass }}">
                                        {{ $status ?: '-' }}
                                    </span>
                                </td>
                                <td>{{ $createdAt ?? '-' }}</td>
                                <td>
                                    @if ($fotoUrl)
                                        <a href="{{ $fotoUrl }}" target="_blank" class="link-primary text-decoration-none">Lihat Foto</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('laporan.show', $laporan->id) }}" class="btn btn-outline-primary btn-sm">
                                            Detail
                                        </a>
                                        <a href="{{ route('laporan.edit', $laporan->id) }}" class="btn btn-outline-secondary btn-sm">
                                            Edit
                                        </a>
                                        <form action="{{ route('laporan.destroy', $laporan->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus laporan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Belum ada laporan. Yuk buat laporan pertama Anda!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
            <p class="text-muted small mt-2 mb-0">
                Keterangan status: 
                <span class="badge bg-primary-subtle text-primary">Baru Masuk</span> = menunggu verifikasi,
                <span class="badge bg-warning-subtle text-warning-emphasis">Sedang Diverifikasi</span> = diproses petugas,
                <span class="badge bg-success-subtle text-success-emphasis">Selesai Ditindaklanjuti</span> = tindak lanjut selesai.
            </p>
        @endif
    </div>
</div>
@endsection
