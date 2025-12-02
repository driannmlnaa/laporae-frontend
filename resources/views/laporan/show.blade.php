@extends('layouts.app')

@section('title', 'Detail Laporan - ' . $laporan->judul)

@section('content')
@php
    $badgeClass = match ($laporan->status) {
        'Baru Masuk' => 'bg-primary-subtle text-primary',
        'Sedang Diverifikasi' => 'bg-warning-subtle text-warning-emphasis',
        'Selesai Ditindaklanjuti' => 'bg-success-subtle text-success-emphasis',
        default => 'bg-secondary-subtle text-secondary-emphasis',
    };
    $fotoUrl = $laporan->foto_url;
@endphp

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1 class="h4 mb-1">{{ $laporan->judul }}</h1>
            <p class="text-muted mb-0">Dilaporkan pada {{ $laporan->created_at?->format('d M Y H:i') }}</p>
        </div>
        <span class="badge rounded-pill {{ $badgeClass }}">{{ $laporan->status }}</span>
    </div>

    <div class="card-body">
        <div class="row g-4">
            <div class="col-lg-7">
                <h2 class="h5 mb-3">Informasi Laporan</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Kategori</dt>
                    <dd class="col-sm-8">{{ $laporan->kategori }}</dd>

                    <dt class="col-sm-4 text-muted">Lokasi Kejadian</dt>
                    <dd class="col-sm-8">{{ $laporan->lokasi }}</dd>

                    <dt class="col-sm-4 text-muted">Pelapor</dt>
                    <dd class="col-sm-8">{{ $laporan->pelapor?->nama_lengkap ?? 'Saya' }}</dd>
                </dl>

                <div class="mt-4">
                    <h3 class="h6 text-uppercase text-muted">Deskripsi</h3>
                    <p class="mb-0">{{ $laporan->deskripsi }}</p>
                </div>
            </div>

            @if ($fotoUrl)
                <div class="col-lg-5">
                    <h2 class="h5 mb-3">Dokumentasi</h2>
                    <div class="card border-0 shadow-sm">
                        <img src="{{ $fotoUrl }}" class="card-img-top" alt="Foto laporan {{ $laporan->judul }}">
                        <div class="card-body">
                            <p class="card-text text-muted small">Foto pendukung laporan.</p>
                            <a href="{{ $fotoUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">Lihat ukuran penuh</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card-footer bg-white border-0 d-flex flex-wrap gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
        <a href="{{ route('laporan.edit', $laporan->id) }}" class="btn btn-outline-primary">Edit Laporan</a>
        <form action="{{ route('laporan.destroy', $laporan->id) }}" method="POST" onsubmit="return confirm('Hapus laporan ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Hapus Laporan</button>
        </form>
        <a href="{{ route('lapor.create') }}" class="btn btn-primary">Buat Laporan Baru</a>
    </div>
</div>
@endsection
