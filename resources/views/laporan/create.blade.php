@extends('layouts.app')

@section('title', 'Buat Laporan - ' . config('app.name', 'LaporAE'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h1 class="h4 mb-1">Buat Laporan Baru</h1>
        <p class="text-muted mb-0">Isi detail kejadian secara lengkap agar petugas dapat menindaklanjuti lebih cepat.</p>
    </div>

    <div class="card-body">
        @include('lapor.form')
    </div>
</div>
@endsection
