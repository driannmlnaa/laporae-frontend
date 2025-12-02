@extends('layouts.admin')

@section('title', 'Dashboard Admin - ' . config('app.name', 'LaporAE'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1 class="h4 mb-1">Daftar Laporan Masuk</h1>
            <p class="text-muted mb-0">Kelola laporan pengguna secara cepat dan terstruktur.</p>
        </div>
        <span class="badge bg-primary-subtle text-primary-emphasis">Admin: {{ $admin['nama'] ?? 'Admin' }}</span>
    </div>

    <div class="card-body">
        @if ($laporans->isEmpty())
            <p class="text-muted mb-0 text-center py-4">Belum ada laporan yang masuk.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Pelapor</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Dokumentasi</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($laporans as $laporan)
                            @php
                                $statusClass = match ($laporan->status) {
                                    'Baru Masuk' => 'bg-primary-subtle text-primary',
                                    'Sedang Diverifikasi' => 'bg-warning-subtle text-warning-emphasis',
                                    'Selesai Ditindaklanjuti' => 'bg-success-subtle text-success-emphasis',
                                    default => 'bg-secondary-subtle text-secondary-emphasis',
                                };
                                $fotoUrl = $laporan->foto_url;
                            @endphp
                            <tr>
                                <td>{{ $laporan->id }}</td>
                                <td>{{ $laporan->judul }}</td>
                                <td>{{ $laporan->pelapor->nama_lengkap ?? ($laporan->pelapor->email ?? 'Tidak diketahui') }}</td>
                                <td>{{ $laporan->kategori }}</td>
                                <td><span class="badge rounded-pill {{ $statusClass }}">{{ $laporan->status }}</span></td>
                                <td>{{ $laporan->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    @if ($fotoUrl)
                                        <a href="{{ $fotoUrl }}" target="_blank" class="link-primary text-decoration-none">Lihat Foto</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.laporan.edit', $laporan->id) }}" class="btn btn-sm btn-outline-primary">Ubah Status</a>
                                        <form action="{{ route('admin.laporan.destroy', $laporan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
