<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
</head>
<body>
    <h1>Dashboard Admin</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <p>Halo, {{ $admin['nama'] ?? 'Admin' }}.</p>

    <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
    </form>


    <h2>Daftar Semua Laporan</h2>

    @if ($laporans->isEmpty())
        <p>Tidak ada laporan.</p>
    @else
        <table border="1" cellpadding="4" cellspacing="0">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Pelapor</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($laporans as $laporan)
                    <tr>
                        <td>{{ $laporan->judul }}</td>
                        <td>{{ $laporan->pelapor?->nama_lengkap ?? 'Tidak diketahui' }}</td>
                        <td>{{ $laporan->kategori }}</td>
                        <td>{{ $laporan->lokasi }}</td>
                        <td>{{ $laporan->status }}</td>
                        <td>
                            <a href="{{ route('admin.laporan.edit', $laporan->id) }}">Edit Status</a>
                            <form method="POST" action="{{ route('admin.laporan.destroy', $laporan->id) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
