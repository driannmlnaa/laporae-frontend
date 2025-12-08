@extends('layouts.app')

@section('title', 'Detail Laporan - ' . ($laporan->judul ?? 'Laporan'))

@section('content')
@php
    // Normalisasi: kalau array, ubah dulu jadi object
    $lap = is_array($laporan) ? (object) $laporan : $laporan;

    $status = $lap->status ?? '';

    // Di backend kolomnya "foto", bukan "foto_url"
    $fotoPath = $lap->foto ?? null;

    // Base URL backend, sama seperti di UserController
    $backend = config('services.backend.url');

    // Bangun URL lengkap untuk gambar
    $fotoUrl = $fotoPath
        ? rtrim($backend, '/') . '/' . ltrim($fotoPath, '/')
        : null;

    // Format tanggal created_at (string) → d M Y H:i
    $createdAt = null;
    if (!empty($lap->created_at)) {
        try {
            $createdAt = \Carbon\Carbon::parse($lap->created_at)->format('d M Y H:i');
        } catch (\Throwable $e) {
            $createdAt = $lap->created_at; // fallback: tampilkan apa adanya
        }
    }

    // Badge class untuk status
    $badgeClass = match ($status) {
        'Baru Masuk'              => 'bg-primary-subtle text-primary',
        'Sedang Diverifikasi'     => 'bg-warning-subtle text-warning-emphasis',
        'Selesai Ditindaklanjuti' => 'bg-success-subtle text-success-emphasis',
        default                   => 'bg-secondary-subtle text-secondary-emphasis',
    };
@endphp

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1 class="h4 mb-1">{{ $lap->judul ?? '-' }}</h1>
            <p class="text-muted mb-0">
                Dilaporkan pada {{ $createdAt ?? '-' }}
            </p>
        </div>
        <span class="badge rounded-pill {{ $badgeClass }}">{{ $status ?: '-' }}</span>
    </div>

    <div class="card-body">
        <div class="row g-4">
            <div class="col-lg-7">
                <h2 class="h5 mb-3">Informasi Laporan</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Kategori</dt>
                    <dd class="col-sm-8">{{ $lap->kategori ?? '-' }}</dd>

                    <dt class="col-sm-4 text-muted">Lokasi Kejadian</dt>
                    <dd class="col-sm-8">{{ $lap->lokasi ?? '-' }}</dd>

                    <dt class="col-sm-4 text-muted">Pelapor</dt>
                    <dd class="col-sm-8">{{ $lap->pelapor->nama_lengkap ?? 'Saya' }}</dd>
                </dl>

                <div class="mt-4">
                    <h3 class="h6 text-uppercase text-muted">Deskripsi</h3>
                    <p class="mb-0">{{ $lap->deskripsi ?? '-' }}</p>
                </div>
            </div>

            @if ($fotoUrl)
                <div class="col-lg-5">
                    <h2 class="h5 mb-3">Dokumentasi</h2>
                    <div class="card border-0 shadow-sm">
                        <img src="{{ $fotoUrl }}" class="card-img-top" alt="Foto laporan {{ $lap->judul }}">
                        <div class="card-body">
                            <p class="card-text text-muted small">Foto pendukung laporan.</p>
                            <a href="{{ $fotoUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                Lihat ukuran penuh
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card-footer bg-white border-0 d-flex flex-wrap gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
        <a href="{{ route('laporan.edit', $lap->id) }}" class="btn btn-outline-primary">Edit Laporan</a>
        <form action="{{ route('laporan.destroy', $lap->id) }}" method="POST" onsubmit="return confirm('Hapus laporan ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Hapus Laporan</button>
        </form>
        <a href="{{ route('lapor.create') }}" class="btn btn-primary">Buat Laporan Baru</a>
    </div>
</div>
@endsection
