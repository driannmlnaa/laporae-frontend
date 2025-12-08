@extends('layouts.admin')

@section('title', 'Ubah Status Laporan - ' . config('app.name', 'LaporAE'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h1 class="h5 mb-1">Ubah Status Laporan</h1>
        <p class="text-muted mb-0">ID #{{ $laporan->id }} — {{ $laporan->judul }}</p>
    </div>

    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.laporan.update', $laporan->id) }}" method="POST" class="mt-2">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="status" class="form-label">Status Laporan</label>
                <select name="status" id="status" class="form-select" required>
                    @php $current = $laporan->status ?? 'Baru Masuk'; @endphp
                    <option value="Baru Masuk" {{ $current === 'Baru Masuk' ? 'selected' : '' }}>Baru Masuk</option>
                    <option value="Sedang Diverifikasi" {{ $current === 'Sedang Diverifikasi' ? 'selected' : '' }}>Sedang Diverifikasi</option>
                    <option value="Selesai Ditindaklanjuti" {{ $current === 'Selesai Ditindaklanjuti' ? 'selected' : '' }}>Selesai Ditindaklanjuti</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
