@extends('layouts.app')

@section('title', 'Edit Laporan - ' . config('app.name', 'LaporAE'))

@section('content')
@php
    $lap = is_array($laporan) ? (object) $laporan : $laporan;
    $fotoPath = $lap->foto ?? null;
    $backend  = config('services.backend.url');
    $fotoUrl  = $fotoPath
        ? rtrim($backend, '/') . '/' . ltrim($fotoPath, '/')
        : null;
@endphp
@if ($fotoUrl)
    <img src="{{ $fotoUrl }}" alt="Foto Laporan" class="img-fluid mb-2">
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h1 class="h4 mb-1">Edit Laporan</h1>
        <p class="text-muted mb-0">Perbarui informasi laporan Anda bila ada perubahan.</p>
    </div>

    <div class="card-body">
        @include('lapor.form', ['laporan' => $laporan])
    </div>
</div>
@endsection
