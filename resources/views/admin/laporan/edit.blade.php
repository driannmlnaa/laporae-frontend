@extends('layouts.admin')

@section('title', 'Ubah Status Laporan #' . $laporan->id)

@section('content')
@php $fotoUrl = $laporan->foto_url; @endphp

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1 class="h4 mb-1">Ubah Status Laporan</h1>
            <p class="text-muted mb-0">Perbarui status laporan untuk memberi informasi terbaru kepada pelapor.</p>
        </div>
        <span class="badge bg-secondary-subtle text-secondary-emphasis">ID #{{ $laporan->id }}</span>
    </div>

    <div class="card-body">
        <div class="row g-4">
            <div class="col-lg-7">
                <h2 class="h5 mb-3">Ringkasan Laporan</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Judul</dt>
                    <dd class="col-sm-8">{{ $laporan->judul }}</dd>
                    <dt class="col-sm-4 text-muted">Pelapor</dt>
                    <dd class="col-sm-8">{{ $laporan->pelapor->nama_lengkap ?? ($laporan->pelapor->email ?? 'Tidak diketahui') }}</dd>
                    <dt class="col-sm-4 text-muted">Kategori</dt>
                    <dd class="col-sm-8">{{ $laporan->kategori }}</dd>
                    <dt class="col-sm-4 text-muted">Lokasi</dt>
                    <dd class="col-sm-8">{{ $laporan->lokasi }}</dd>
                </dl>

                <div class="mt-4">
                    <h3 class="h6 text-uppercase text-muted">Deskripsi</h3>
                    <p class="mb-0">{{ $laporan->deskripsi }}</p>
                </div>
            </div>

            <div class="col-lg-5">
                @if ($fotoUrl)
                    <h2 class="h6 text-uppercase text-muted mb-3">Dokumentasi</h2>
                    <div class="card border-0 shadow-sm">
                        <img src="{{ $fotoUrl }}" class="card-img-top" alt="Foto laporan {{ $laporan->judul }}">
                        <div class="card-body">
                            <p class="card-text text-muted small">Pratinjau foto yang dilampirkan oleh pelapor.</p>
                            <a href="{{ $fotoUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">Lihat ukuran penuh</a>
                        </div>
                    </div>
                @else
                    <p class="text-muted">Tidak ada dokumentasi yang dilampirkan.</p>
                @endif
            </div>
        </div>

        <hr class="my-4">

        <form method="POST" action="{{ route('admin.laporan.update', $laporan->id) }}" class="row gy-3">
            @csrf
            @method('PUT')

            <div class="col-md-6 col-lg-4">
                <label for="status" class="form-label">Status Laporan</label>
                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="Baru Masuk" @selected(old('status', $laporan->status) === 'Baru Masuk')>Baru Masuk</option>
                    <option value="Sedang Diverifikasi" @selected(old('status', $laporan->status) === 'Sedang Diverifikasi')>Sedang Diverifikasi</option>
                    <option value="Selesai Ditindaklanjuti" @selected(old('status', $laporan->status) === 'Selesai Ditindaklanjuti')>Selesai Ditindaklanjuti</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
