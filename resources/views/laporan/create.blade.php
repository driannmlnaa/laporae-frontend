@extends('layouts.app')

@section('title', 'Buat Laporan - ' . config('app.name', 'LaporAE'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">Buat Laporan Baru</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('lapor.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Laporan</label>
                        <input type="text" name="judul" id="judul"
                               class="form-control"
                               value="{{ old('judul') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4"
                                  class="form-control"
                                  required>{{ old('deskripsi') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select name="kategori" id="kategori" class="form-select" required>
                            <option value="">-- Pilih kategori --</option>
                            @foreach ($kategoriOptions as $kat)
                                <option value="{{ $kat }}" @selected(old('kategori') === $kat)>{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" id="lokasi"
                               class="form-control"
                               value="{{ old('lokasi') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto (bukti)</label>
                        <input type="file" name="foto" id="foto"
                               class="form-control" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
