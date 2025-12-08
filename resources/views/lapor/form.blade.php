{{-- resources/views/lapor/form.blade.php --}}
@extends('layouts.app')

@section('title', $isEdit ? 'Edit Laporan' : 'Buat Laporan Baru')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">
            {{ $isEdit ? 'Edit Laporan' : 'Buat Laporan Baru' }}
        </h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            Kembali ke Dashboard
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($message = session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $lap = $laporan ? (object) $laporan : null;
    @endphp

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form
                method="POST"
                action="{{ $isEdit ? route('laporan.update', $lap->id) : route('lapor.store') }}"
                enctype="multipart/form-data"
            >
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Laporan</label>
                    <input
                        type="text"
                        id="judul"
                        name="judul"
                        class="form-control @error('judul') is-invalid @enderror"
                        value="{{ old('judul', $lap->judul ?? '') }}"
                        required
                    >
                    @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Kejadian</label>
                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        rows="4"
                        class="form-control @error('deskripsi') is-invalid @enderror"
                        required
                    >{{ old('deskripsi', $lap->deskripsi ?? '') }}</textarea>
                    @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select
                        id="kategori"
                        name="kategori"
                        class="form-select @error('kategori') is-invalid @enderror"
                        required
                    >
                        @php
                            $kat = old('kategori', $lap->kategori ?? '');
                        @endphp
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Keamanan" {{ $kat === 'Keamanan' ? 'selected' : '' }}>Keamanan</option>
                        <option value="Aksesibilitas" {{ $kat === 'Aksesibilitas' ? 'selected' : '' }}>Aksesibilitas</option>
                        <option value="Fasilitas Rusak" {{ $kat === 'Fasilitas Rusak' ? 'selected' : '' }}>Fasilitas Rusak</option>
                    </select>
                    @error('kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="lokasi" class="form-label">Lokasi Kejadian</label>
                    <input
                        type="text"
                        id="lokasi"
                        name="lokasi"
                        class="form-control @error('lokasi') is-invalid @enderror"
                        value="{{ old('lokasi', $lap->lokasi ?? '') }}"
                        required
                    >
                    @error('lokasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="foto" class="form-label">Unggah Foto Bukti</label>
                    <input
                        type="file"
                        id="foto"
                        name="foto"
                        accept="image/*"
                        class="form-control @error('foto') is-invalid @enderror"
                        {{ $isEdit ? '' : 'required' }}
                    >
                    <div class="form-text">
                        Format diperbolehkan: JPG, PNG. Maksimal 2MB.
                    </div>
                    @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror

                    @if ($isEdit && !empty($lap->foto))
                        @php
                            $backend = rtrim(config('services.backend.url'), '/');
                            $fotoUrl = $backend . '/' . ltrim($lap->foto, '/');
                        @endphp
                        <div class="mt-2">
                            <a href="{{ $fotoUrl }}" target="_blank" class="text-decoration-none">
                                Lihat foto saat ini
                            </a>
                        </div>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        {{ $isEdit ? 'Simpan Perubahan' : 'Kirim Laporan' }}
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
